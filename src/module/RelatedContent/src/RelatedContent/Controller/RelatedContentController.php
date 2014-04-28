<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace RelatedContent\Controller;

use Authorization\Service\AuthorizationAssertionTrait;
use RelatedContent\Form\CategoryForm;
use RelatedContent\Form\ExternalForm;
use RelatedContent\Form\InternalForm;
use RelatedContent\Manager\RelatedContentManagerAwareTrait;
use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

class RelatedContentController extends AbstractActionController
{
    use RelatedContentManagerAwareTrait;

    public function addCategoryAction()
    {
        $this->assertGranted('related.content.create');

        $form = new CategoryForm();
        $view = new ViewModel(['form' => $form]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getRelatedContentManager()->addCategory((int)$this->params('id'), $data['title']);
                $this->getRelatedContentManager()->getObjectManager()->flush();
                $this->redirect()->toRoute('related-content/manage', ['id' => $this->params('id')]);
            }
        }
        $view->setTemplate('related-content/add-category');
        $this->layout('layout/1-col');
        return $view;
    }

    public function addExternalAction()
    {
        $container = $this->getContainer();
        $form      = new ExternalForm();
        $view      = new ViewModel(['form' => $form]);
        $this->assertGranted('related.content.create', $container);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getRelatedContentManager()->addExternal($container->getId(), $data['title'], $data['url']);
                $this->getRelatedContentManager()->getObjectManager()->flush();
                $this->redirect()->toRoute('related-content/manage', ['id' => $this->params('id')]);
            }
        }
        $view->setTemplate('related-content/add-external');
        $this->layout('layout/1-col');
        return $view;
    }

    public function addInternalAction()
    {
        $container = $this->getContainer();
        $form      = new InternalForm();
        $view      = new ViewModel(['form' => $form]);
        $this->assertGranted('related.content.create', $container);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getRelatedContentManager()->addInternal(
                    $container->getId(),
                    $data['title'],
                    $data['reference']
                );
                $this->getRelatedContentManager()->getObjectManager()->flush();
                $this->redirect()->toRoute('related-content/manage', ['id' => $this->params('id')]);
            }
        }
        $view->setTemplate('related-content/add-internal');
        $this->layout('layout/1-col');
        return $view;
    }

    public function getContainer($id = null)
    {
        $id = $id ? : $this->params('id');
        return $this->getRelatedContentManager()->getContainer($id);
    }

    public function manageAction()
    {
        $container  = $this->getContainer();
        $aggregated = $this->getRelatedContentManager()->aggregateRelatedContent($this->params('id'));
        $view       = new ViewModel([
            'aggregated' => $aggregated,
            'container'  => $container
        ]);
        $view->setTemplate('related-content/manage');
        $this->layout('layout/1-col');
        return $view;
    }

    public function orderAction()
    {
        $position = 1;
        if ($this->getRequest()->isPost()) {
            foreach ($this->params()->fromPost('sortable', []) as $holder) {
                $this->getRelatedContentManager()->positionHolder((int)$holder['id'], (int)$position);
                $position++;
            }
        }
        $this->getRelatedContentManager()->getObjectManager()->flush();

        return false;
    }

    public function removeAction()
    {
        $this->getRelatedContentManager()->removeRelatedContent((int)$this->params('id'));
        $this->getRelatedContentManager()->getObjectManager()->flush();
        return $this->redirect()->toReferer();
    }
}
