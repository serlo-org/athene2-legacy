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
namespace Taxonomy\Controller;

use Zend\View\Model\ViewModel;
use Taxonomy\Form\TermForm;

class TermController extends AbstractController
{
    use \User\Manager\UserManagerAwareTrait;

    public function organizeAction(){
        $term = $this->getTerm();
    
        $view = new ViewModel(array(
            'term' => $term,
        ));
    
        $view->setTemplate('taxonomy/term/organize');
        return $view;
    }
    
    public function updateAction()
    {
        $id = $this->params('id');
        $term = $this->getTerm($id);
        
        $view = new ViewModel(array(
            'id' => $id,
            'isUpdating' => true
        ));
        
        $form = new TermForm();
        
        $form->setData($term->getArrayCopy());
        $view->setVariable('form', $form);
        
        $view->setTemplate('/taxonomy/term/form');
        
        if ($this->getRequest()->isPost()) {
            $form->setData($this->getRequest()
                ->getPost());
            if ($form->isValid()) {
                $this->getSharedTaxonomyManager()->updateTerm($this->params('id'), $form->getData());
                
                $this->getEventManager()->trigger('update', $this, array(
                    'term' => $term,
                    'user' => $this->getUserManager()->getUserFromAuthenticator(),
                    'language' => $this->getLanguageManager()->getLanguageFromRequest(),
                    'post' => $form->getData()
                ));
                
                $this->getSharedTaxonomyManager()
                    ->getObjectManager()
                    ->flush();
                $this->flashMessenger()->addSuccessMessage('Your changes have been saved!');
                $this->redirect()->toUrl($this->referer()->fromStorage());
            }
        }
        $this->referer()->store();
        
        return $view;
    }

    public function createAction()
    {
        $form = new TermForm();

        $form->setData(array(
            'taxonomy' => $this->params('taxonomy'),
            'parent' => $this->params('parent', null)
        ));
        
        $form->setAttribute('action', $this->url()
            ->fromRoute('taxonomy/term/create', array(
            'parent' => $this->params('parent'),
            'taxonomy' => $this->params('taxonomy')
        )));
        
        $view = new ViewModel(array(
            'form' => $form,
            'isUpdating' => false
        ));
        
        $view->setTemplate('/taxonomy/term/form');
        
        if ($this->getRequest()->isPost()) {
            $data = $this->getRequest()->getPost();
            $form->setData($data);
            if ($form->isValid()) {
                $term = $this->getSharedTaxonomyManager()->createTerm($form->getData(), $this->getLanguageManager()
                    ->getLanguageFromRequest());
                
                $this->getEventManager()->trigger('create', $this, array(
                    'term' => $term,
                    'user' => $this->getUserManager()->getUserFromAuthenticator(),
                    'language' => $this->getLanguageManager()->getLanguageFromRequest(),
                    'post' => $form->getData()
                ));
                
                $this->getSharedTaxonomyManager()
                    ->getObjectManager()
                    ->flush();

                $this->flashMessenger()->addSuccessMessage('The node has been added successfully!');
                $this->redirect()->toUrl($this->referer()->fromStorage());
            }
        }
        $this->referer()->store();
        return $view;
    }

    public function orderAssociatedAction()
    {
        $association = $this->params('association');        
        $termService = $this->getTerm($this->params('term'));
        
        if($this->getRequest()->isPost()){
            $associations = $this->params()->fromPost('sortable', array());
            $i = 0;
            
            foreach ($associations as $a) {
                $termService->positionAssociatedObject($association, $a['id'], $i);
                $i++;
            }
            
            $this->getSharedTaxonomyManager()
                ->getObjectManager()
                ->flush();
            
            return '';
        }   
        
        $associations = $termService->getAssociated($association);
        $view = new ViewModel(array(
            'term' => $termService,
            'associations' => $associations,
            'association' => $association
        ));
        $view->setTemplate('taxonomy/term/order-associated');
        
        return $view;     
    }

    public function orderAction()
    {
        $data = $this->params()->fromPost('sortable', array());
        $this->iterWeight($data, $this->params('term'));
        $this->getSharedTaxonomyManager()
            ->getObjectManager()
            ->flush();
        return '';
    }

    protected function iterWeight($terms, $parent = NULL)
    {
        $weight = 1;
        foreach ($terms as $term) {
            $entity = $this->getTerm($term['id']);
            $oldParent = $entity->getParent();
            if ($parent) {
                $entity->setParent($this->getTerm($parent));
            } else {
                $entity->setParent(NULL);
            }
            $entity->setPosition($weight);
            $this->getSharedTaxonomyManager()->getObjectManager()->persist($entity->getEntity());
            
            if($oldParent !== $entity->getParent()){
                $this->getEventManager()->trigger('parent-change', $this, array(
                    'term' => $entity,
                    'old' => $oldParent,
                    'new' => $entity->getParent(),
                    'user' => $this->getUserManager()->getUserFromAuthenticator(),
                    'language' => $this->getLanguageManager()->getLanguageFromRequest(),
                ));
            }
            
            if (isset($term['children'])) {
                $this->iterWeight($term['children'], $term['id']);
            }
            $weight ++;
        }
        return true;
    }
}