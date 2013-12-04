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
namespace Entity\Plugin\License\Form;

use Zend\Form\Form;
use Zend\Form\Element\Submit;
use Zend\InputFilter\InputFilter;
use Zend\Form\Element\Select;

class LicenseForm extends Form
{
    public function __construct(array $licenses){

        parent::__construct('context');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'clearfix');
        $inputFilter = new InputFilter('context');
        $this->setInputFilter($inputFilter);
        
        $this->add(array(
            'name' => 'route',
            'type' => 'Hidden',
            'attributes' => array()
        ));
        
        $values = array();
        foreach ($licenses as $license) {
            $values[$license->getId()] = $license->getTitle();
        }
        
        $this->add((new Select('license'))->setLabel('Select a license:')->setValueOptions($values));
        
        $this->add((new Submit('submit'))->setValue('Update')
            ->setAttribute('class', 'btn btn-success pull-right'));
    }
}