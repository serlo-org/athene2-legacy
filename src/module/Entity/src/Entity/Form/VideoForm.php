<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author         Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license        LGPL-3.0
 * @license        http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link           https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright      Copyright (c) 2013 Gesellschaft f√ºr freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Entity\Form;

use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Url;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class VideoForm extends Form
{

    function __construct()
    {
        parent::__construct('video');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'clearfix');

        $this->add((new Text('title'))->setLabel('Title:'));
        $this->add((new Url('content'))->setLabel('Video url:'));
        $this->add(
            (new Textarea('reasoning'))->setLabel('Reasoning:')->setAttribute(
                'class',
                'plain'
            )
        );
        $this->add(
            (new Textarea('Changes'))->setLabel('Changes:')->setAttribute(
                'class',
                'plain'
            )
        );
        $this->add(new Controls());

        $inputFilter = new InputFilter('video');
        $inputFilter->add(['name' => 'title', 'required' => true, 'filters' => [['name' => 'StripTags']]]);
        $inputFilter->add(['name' => 'content', 'required' => true, 'filters' => [['name' => 'StripTags']]]);
        $inputFilter->add(['name' => 'reasoning', 'required' => false, 'filters' => [['name' => 'StripTags']]]);
        $inputFilter->add(['name' => 'changes', 'required' => false, 'filters' => [['name' => 'StripTags']]]);
        $this->setInputFilter($inputFilter);
    }
}