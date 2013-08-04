<?php

/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author	Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license	LGPL-3.0
 * @license	http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link		https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Application\Entity\Plugin\Repository\Controller;

use Versioning\Exception\RevisionNotFoundException;
use Zend\View\Model\ViewModel;
use Entity\Plugin\Controller\AbstractController;

class RepositoryController extends AbstractController
{

    public function createRevisionAction ()
    {
        $repository = $plugin = $this->getPlugin();
        $entity = $plugin->getEntityService();
        if ($this->getRequest()->isPost()) {
            $this->commitRevision($entity);
            if ($entity->isCheckedOut()) {
                $this->redirect()->toRoute('entity/plugin/repository', array(
                    'action' => 'show',
                    'id' => $entity->getId()
                ));
            } else {
                $this->redirect()->toRoute('entity/plugin/repository', array(
                    'action' => 'history',
                    'id' => $entity->getId()
                ));
            }
        }
        $view = new ViewModel(array(
            'entity' => $entity
        ));
        
        $view->setTemplate('entity/plugin/repository/update-revision');
        $view->setVariable('form', $entity->getForm());
        
        return $view;
    }

    public function compareAction ()
    {
        $repository = $plugin = $this->getPlugin();
        $entity = $plugin->getEntityService();
        $revision = $this->_getRevision($repository, $this->getParam('revision'), FALSE);
        $currentRevision = $this->_getRevision($repository);
        
        $view = new ViewModel(array(
            'currentRevision' => $currentRevision,
            'revision' => $revision,
            'entity' => $entity
        ));
        
        $view->setTemplate('entity/plugin/repository/compare-revision');
        
        $revisionView = $this->getRevision($this->getParam('revision'));
        $currentRevisionView = $this->getRevision();
        
        $view->addChild($revisionView, 'revisionView');
        
        if ($currentRevisionView) {
            $view->addChild($currentRevisionView, 'currentRevisionView');
        }
        
        return $view;
    }

    public function historyAction ()
    {
        $repository = $plugin = $this->getPlugin();
        $entity = $plugin->getEntityService();
        try {
            $currentRevision = $entity->getCurrentRevision();
        } catch (RevisionNotFoundException $e) {
            $currentRevision = NULL;
        }
        $repositoryView = new ViewModel(array(
            'entity' => $entity,
            'revisions' => $repository->getAllRevisions(),
            'trashedRevisions' => $repository->getTrashedRevisions(),
            'currentRevision' => $currentRevision
        ));
        
        $repositoryView->setTemplate('entity/plugin/repository/history');
        return $repositoryView;
    }

    protected function _getRevision ($repository, $id = NULL, $catch = TRUE)
    {
        if ($catch) {
            try {
                if ($id === NULL) {
                    return $repository->getCurrentRevision();
                } else {
                    return $repository->getRevision($id);
                }
            } catch (RevisionNotFoundException $e) {
                return NULL;
            }
        } else {
            if ($id === NULL) {
                return $repository->getCurrentRevision();
            } else {
                return $repository->getRevision($id);
            }
        }
    }

    public function checkoutAction ()
    {
        $repository = $plugin = $this->getPlugin();
        $entity = $plugin->getEntityService();
        $repository->checkout($this->getParam('revision'));
        $this->redirect()->toRoute('entity/plugin/repository', array(
            'action' => 'history',
            'entity' => $entity->getId()
        ));
    }

    public function purgeRevisionAction ()
    {
        $repository = $plugin = $this->getPlugin();
        $entity = $plugin->getEntityService();
        $repository->removeRevision($this->getParam('revision'));
        $this->redirect()->toRoute('entity/plugin/repository', array(
            'action' => 'history',
            'entity' => $entity->getId()
        ));
    }

    public function trashRevisionAction ()
    {
        $repository = $plugin = $this->getPlugin();
        $entity = $plugin->getEntityService();
        $repository->trashRevision($this->getParam('revision'));
        $this->redirect()->toRoute('entity/plugin/repository', array(
            'action' => 'history',
            'entity' => $entity->getId()
        ));
    }

    public function getHeadAction (){
        return $this->getRevision();
    }

    public function revisionAction ()
    {
        return $this->getRevision($this->params('revision'));
    }

    public function getRevision ($revisionId = NULL)
    {
        $repository = $plugin = $this->getPlugin();
        $entity = $plugin->getEntityService();
        $revision = $this->_getRevision($repository, $revisionId);
        $view = new ViewModel(array(
            'entity' => $entity,
            'repository' => $repository,
            'revision' => $revision
        ));
        $view->setTemplate('entity/plugin/repository/revision');
        return $view;
    }

    protected function commitRevision ($entity)
    {
        $form = $entity->getForm();
        $form->setData($this->getRequest()
            ->getPost());
        if ($form->isValid()) {
            $data = $form->getData();
            $entity->commitRevision($data['repository']['revision']);
            $this->flashMessenger()->addSuccessMessage('Deine Bearbeitung wurde gespeichert. Du erhälst eine Benachrichtigung, sobald deine Bearbeitung geprüft wird.');
        }
        return $entity;
    }
}