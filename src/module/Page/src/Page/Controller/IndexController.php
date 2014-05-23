<?php
namespace Page\Controller;

use Alias\AliasManagerAwareTrait;
use Alias\AliasManagerInterface;
use Common\Traits\ObjectManagerAwareTrait;
use Instance\Manager\InstanceManagerAwareTrait;
use Instance\Manager\InstanceManagerInterface;
use Page\Exception\PageNotFoundException;
use Page\Form\RepositoryForm;
use Page\Form\RevisionForm;
use Page\Manager\PageManagerAwareTrait;
use Page\Manager\PageManagerInterface;
use User\Manager\UserManagerAwareTrait;
use User\Manager\UserManagerInterface;
use Versioning\RepositoryManagerAwareTrait;
use Versioning\RepositoryManagerInterface;
use Zend\Form\FormInterface;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class IndexController extends AbstractActionController
{
    use InstanceManagerAwareTrait, RepositoryManagerAwareTrait;
    use PageManagerAwareTrait;
    use UserManagerAwareTrait, AliasManagerAwareTrait;

    /**
     * @var RepositoryForm
     */
    protected $repositoryForm;

    /**
     * @var RevisionForm
     */
    protected $revisionForm;

    public function __construct(
        AliasManagerInterface $aliasManager,
        InstanceManagerInterface $instanceManager,
        PageManagerInterface $pageManager,
        RepositoryForm $repositoryForm,
        RevisionForm $revisionForm,
        RepositoryManagerInterface $repositoryManager,
        UserManagerInterface $userManager
    ) {
        $this->aliasManager      = $aliasManager;
        $this->instanceManager   = $instanceManager;
        $this->pageManager       = $pageManager;
        $this->repositoryManager = $repositoryManager;
        $this->userManager       = $userManager;
        $this->repositoryForm    = $repositoryForm;
        $this->revisionForm      = $revisionForm;
    }

    public function checkoutAction()
    {
        $id             = $this->params('revision');
        $pageRepository = $this->getPageRepository();
        if (!$pageRepository) {
            return $this->notFound();
        }

        $this->getRepositoryManager()->getRepository($pageRepository)->checkoutRevision($id);
        $this->getPageManager()->flush();
        return $this->redirect()->toReferer();
    }

    public function createAction()
    {
        $instance = $this->getInstanceManager()->getInstanceFromRequest();
        $form     = $this->repositoryForm;
        $this->assertGranted('page.create', $instance);

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $data = array_merge($data, ['instance' => $instance->getId()]);
            $form->setData($data);
            if ($form->isValid()) {
                $repository = $this->getPageManager()->createPageRepository($form);
                $data       = $form->getData(FormInterface::VALUES_AS_ARRAY);
                $params     = [
                    'repository' => $repository,
                    'slug'       => $data['slug']
                ];
                $this->getEventManager()->trigger('page.create', $this, $params);
                $this->getPageManager()->flush();
                $this->getEventManager()->trigger('page.create.postFlush', $this, $params);
                $this->redirect()->toRoute('page/revision/create', ['page' => $repository->getId()]);
            }
        }

        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('page/create');

        return $view;
    }

    public function createRevisionAction()
    {
        $user = $this->getUserManager()->getUserFromAuthenticator();
        $form = $this->revisionForm;
        $id   = $this->params('revision');
        $page = $this->getPageRepository();
        if (!$page) {
            return $this->notFound();
        }
        $this->assertGranted('page.revision.create', $page);

        if ($id != null) {
            $revision = $this->getPageManager()->getRevision($id);
            $form->get('content')->setValue($revision->getContent());
            $form->get('title')->setValue($revision->getTitle());
        }
        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $array           = $form->getData();
                $array['author'] = $user;
                $this->getPageManager()->createRevision($page, $array, $user);
                $this->getPageManager()->flush();
                return $this->redirect()->toRoute('page/view', ['page' => $page->getId()]);
            }
        }

        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('page/revision/create');
        $this->layout('editor/layout');

        return $view;
    }

    public function getPageRepository()
    {
        try {
            $id = $this->params('page');
            return $this->getPageManager()->getPageRepository($id);
        } catch (PageNotFoundException $e) {
            return false;
        }
    }

    public function indexAction()
    {
        $instance = $this->getInstanceManager()->getInstanceFromRequest();
        $this->assertGranted('page.create', $instance);
        $pages    = $this->getPageManager()->findAllRepositories($instance);
        $view     = new ViewModel(['pages' => $pages]);
        $view->setTemplate('page/pages');
        return $view;
    }

    public function updateAction()
    {
        $instance = $this->getInstanceManager()->getInstanceFromRequest();
        $page     = $this->getPageRepository();
        if (!$page) {
            return $this->notFound();
        }

        $alias    = $this->getAliasManager()->findAliasByObject($page)->getAlias();
        $form     = $this->repositoryForm;

        $this->assertGranted('page.update', $page);
        $form->bind($page);
        $form->get('slug')->setValue($alias);

        if ($this->getRequest()->isPost()) {
            $data = $this->params()->fromPost();
            $form->setData($data);
            if ($form->isValid()) {
                $array  = $form->getData(FormInterface::VALUES_AS_ARRAY);
                $source = $this->url()->fromRoute('page/view', ['page' => $page->getId()]);
                $this->getAliasManager()->createAlias(
                    $source,
                    $array['slug'],
                    $array['slug'] . $page->getId(),
                    $page,
                    $instance
                );
                $this->getPageManager()->editPageRepository($form);
                $this->getPageManager()->flush();
                $this->redirect()->toUrl($source);
            }
        }

        $view = new ViewModel(['form' => $form]);
        $view->setTemplate('page/update');
        return $view;
    }

    public function viewAction()
    {
        $pageRepository = $this->getPageRepository();
        if (!$pageRepository) {
            return $this->notFound();
        }

        $revision = $pageRepository->hasCurrentRevision() ? $pageRepository->getCurrentRevision() : null;
        if (!$revision) {
            return $this->notFound();
        }

        $view = new ViewModel(['revision' => $revision, 'page' => $pageRepository]);

        $this->assertGranted('page.get', $pageRepository);
        $view->setTemplate('page/revision/view');

        return $view;
    }

    public function viewRevisionAction()
    {
        $id             = $this->params('revision');
        $revision       = $this->getPageManager()->getRevision($id);
        $pageRepository = $revision->getRepository();
        $view           = new ViewModel(['revision' => $revision, 'page' => $pageRepository]);

        $this->assertGranted('page.get', $pageRepository);
        $view->setTemplate('page/revision/view');

        return $view;
    }

    public function viewRevisionsAction()
    {
        $pageRepository = $this->getPageRepository();
        if (!$pageRepository) {
            return $this->notFound();
        }

        $revisions      = $pageRepository->getRevisions();
        $view           = new ViewModel(['revisions' => $revisions, 'page' => $pageRepository]);

        $this->assertGranted('page.get', $pageRepository);
        $view->setTemplate('page/revisions');

        return $view;
    }

    protected function notFound()
    {
        $this->getResponse()->setStatusCode(404);
        return false;
    }
}
