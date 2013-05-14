<?php
namespace Entity\LearningObjects\Solution;

use Entity\LearningObjects\Solution\Form\TextSolutionForm;
use Zend\View\Model\ViewModel;

class TextSolution extends AbstractSolution
{       

    protected function _loadComponents(){
        parent::_loadComponents();
        $this->setTemplate('entity/learning-objects/solution/text/display');
    }
     
    public function getContent ()
    {
        return $this->_repositoryComponent->getCurrentRevision()->get('content');
    }
    
    public function getData(){
        return array(
            'id' => $this->getId(),
            'content' => $this->getContent()
        );
    }
    
    public function toViewModel()
    {
        if(!$this->_viewModel){
            $this->_viewModel = new ViewModel(array('entity' => $this));
        }
        $this->_viewModel->setTemplate($this->getTemplate());
        return $this->_viewModel;
    }
    
    public function getFormObject(){
        return new TextSolutionForm();
    }
    
    public function getFormData(){
        return array(
            'revision' => array(
                'content' => $this->getContent(),
            )
        );
    }
}