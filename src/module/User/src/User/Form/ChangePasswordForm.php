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
namespace User\Form;

use Zend\Form\Element\Password;
use Zend\Form\Element\Submit;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class ChangePasswordForm extends Form
{
    public function __construct()
    {        
        parent::__construct('settings');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'clearfix');

        $inputFilter = new InputFilter();
        $this->setInputFilter($inputFilter);
        
        $this->add((new Password('currentPassword'))->setLabel('Current password:'));
        $this->add((new Password('password'))->setLabel('New password:'));
        $this->add((new Password('passwordConfirm'))->setLabel('Confirm password:'));
        
        $this->add((new Submit('submit'))->setValue('Update')
            ->setAttribute('class', 'btn btn-success pull-right'));


        $inputFilter->add([
            'name' => 'passwordConfirm',
            'required' => true,
            'validators' => [
                [
                    'name' => 'stringLength',
                    'options' => [
                        'min' => 6
                    ]
                ],
                [
                    'name' => 'identical',
                    'options' => [
                        'token' => 'password'
                    ]
                ]
            ]
        ]);
        
        $inputFilter->add([
            'name' => 'password',
            'required' => true,
            'filters' => [
                [
                    'name' => 'Authentication\HashFilter'
                ]
            ]
        ]);
    }
}