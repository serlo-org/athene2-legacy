<?php

/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Entity\Controller;

use Entity\Entity\EntityInterface;
use Entity\Options\ModuleOptions;
use Versioning\Exception\RevisionNotFoundException;
use Versioning\RepositoryManagerAwareTrait;
use Zend\Form\Form;
use Zend\Mvc\Exception;
use Zend\View\Model\ViewModel;

class RepositoryController extends AbstractController
{
    use RepositoryManagerAwareTrait;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    public function addRevisionAction()
    {
        $entity = $this->getEntity();

        if (!$entity || $entity->isTrashed()) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }

        $this->assertGranted('entity.revision.create', $entity);

        /* @var $form \Zend\Form\Form */
        $form = $this->getForm($entity);
        $view = new ViewModel(['entity' => $entity, 'form' => $form]);

        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()->getPost());
            if ($form->isValid()) {
                $data = $form->getData();
                $this->getRepositoryManager()->commitRevision($entity, $data);
                $this->getEntityManager()->flush();
                $this->flashMessenger()->addSuccessMessage(
                    'Your revision has been saved, it will be available once it get\'s approved'
                );

                return $this->redirect()->toRoute('entity/repository/history', ['entity' => $entity->getId()]);
            }
        }

        $this->layout('athene2-editor');
        $view->setTemplate('entity/repository/update-revision');

        return $view;
    }

    public function checkoutAction()
    {
        $entity = $this->getEntity();
        $reason = $this->params()->fromPost('reason', '');

        if (!$entity || $entity->isTrashed()) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }

        $this->assertGranted('entity.revision.checkout', $entity);

        $this->getRepositoryManager()->checkoutRevision($entity, $this->params('revision'), $reason);
        $this->getRepositoryManager()->flush();

        return $this->redirect()->toRoute('entity/repository/history', ['entity' => $entity->getId()]);
    }

    public function compareAction()
    {
        $entity = $this->getEntity();

        if (!$entity || $entity->isTrashed()) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }

        $revision        = $this->getRevision($entity, $this->params('revision'));
        $currentRevision = $this->getRevision($entity);

        if (!$revision) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }

        $view = new ViewModel([
            'currentRevision' => $currentRevision,
            'revision'        => $revision,
            'entity'          => $entity
        ]);

        $view->setTemplate('entity/repository/compare-revision');

        return $view;
    }

    public function historyAction()
    {
        $entity = $this->getEntity();

        if (!$entity || $entity->isTrashed()) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }

        $currentRevision = $entity->hasCurrentRevision() ? $entity->getCurrentRevision() : null;
        $this->assertGranted('entity.repository.history', $entity);

        $view = new ViewModel([
            'entity'          => $entity,
            'revisions'       => $entity->getRevisions(),
            'currentRevision' => $currentRevision
        ]);

        $view->setTemplate('entity/repository/history');
        return $view;
    }

    public function rejectAction()
    {
        $entity = $this->getEntity();
        $reason = $this->params()->fromPost('reason', '');

        if (!$entity || $entity->isTrashed()) {
            $this->getResponse()->setStatusCode(404);
            return false;
        }

        $this->assertGranted('entity.revision.trash', $entity);
        $this->getRepositoryManager()->rejectRevision($entity, $this->params('revision'), $reason);
        $this->getRepositoryManager()->flush();

        return $this->redirect()->toReferer();
    }

    /**
     * @param ModuleOptions $moduleOptions
     * @return void
     */
    public function setModuleOptions(ModuleOptions $moduleOptions)
    {
        $this->moduleOptions = $moduleOptions;
    }

    /**
     * @param EntityInterface $entity
     * @return Form
     */
    protected function getForm(EntityInterface $entity)
    {
        // Todo: Unhack

        $type = $entity->getType()->getName();
        $form = $this->moduleOptions->getType($type)->getComponent('repository')->getForm();
        $form = $this->getServiceLocator()->get($form);

        if ($entity->hasCurrentRevision()) {
            $data = [];
            foreach ($entity->getCurrentRevision()->getFields() as $field) {
                $data[$field->getName()] = $field->getValue();
            }
            $form->setData($data);
        }

        $license   = $entity->getLicense();
        $agreement = $license->getAgreement() ? $license->getAgreement() : $license->getTitle();
        $form->get('license')->get('agreement')->setLabel($agreement);

        return $form;
    }

    /**
     * @param EntityInterface $entity
     * @param string          $id
     * @return \Versioning\Entity\RevisionInterface|null
     */
    protected function getRevision(EntityInterface $entity, $id = null)
    {
        try {
            if ($id === null) {
                return $entity->getCurrentRevision();
            } else {
                return $this->getRepositoryManager()->findRevision($entity, $id);
            }
        } catch (RevisionNotFoundException $e) {
            return null;
        }
    }
}
