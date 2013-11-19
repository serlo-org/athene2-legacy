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
namespace Contexter\Form;

use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ContextForm extends Form
{

    public function __construct(array $parameters, array $types)
    {
        parent::__construct('context');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'clearfix');
        $inputFilter = new InputFilter('context');
        $this->setInputFilter($inputFilter);
        
        $this->add(array(
            'name' => 'route',
            'type' => 'Hidden',
            'attributes' => array(
            ),
        ));


        $values = array();
        foreach($types as $type){
            $values[$type] = $type;
        }
        
        $this->add(array(
            'type' => 'Zend\Form\Element\Select',
            'name' => 'type',
            'options' => array(
                'label' => 'Select a type',
                'value_options' => $values
            )
        ));
        
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
            'attributes' => array(),
            'options' => array(
                'label' => 'Title:'
            )
        ));
        
        $this->add(array(
            'name' => 'object',
            'type' => 'Text',
            'attributes' => array(
                'class' => 'disabled',
                'placeholder' => '1234'
            ),
            'options' => array(
                'label' => 'Object:'
            )
        ));
        
        /*$this->add(array(
            'type' => 'Zend\Form\Element\Collection',
            'name' => 'categories',
            'options' => array(
                'label' => 'Please choose which parameters should be enabled',
                'count' => 2,
                //'should_create_template' => true,
                //'template_placeholder' => '__placeholder__',
                'target_element' => array(
                    'type' => 'Contexter\Form\ParameterFieldset'
                )
            )
        ));*/
        
        foreach($parameters as $key => $value){
            //$this->add(new ParameterFieldset($key, $name));
        
            $this->add(array(
                'name' => $key,
                'type' => 'Zend\Form\Element\Checkbox',
                'attributes' => array(
                    'checked' => true
                ),
                'options' => array(
                    'label' => '<strong>' . $key . ':</strong> ' . $value . ''
                )
            ));
        }
        
        $this->add(array(
            'name' => 'submit',
            'type' => 'submit',
            'attributes' => array(
                'value' => 'Speichern',
                'class' => 'btn btn-success pull-right'
            )
        ));
        
        $inputFilter->add(array(
            'name' => 'title',
            'required' => true,
            'filters' => array()
        ));
        
        $inputFilter->add(array(
            'name' => 'object',
            'required' => true,
            'filters' => array()
        ));
    }
}