<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright   Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace License\Form;

use License\Hydrator\LicenseHydrator;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\Form\Element\Url;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class LicenseForm extends Form
{
    public function __construct()
    {
        parent::__construct('license');
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'clearfix');
        $this->setHydrator(new LicenseHydrator());
        $inputFilter = new InputFilter('license');
        $this->setInputFilter($inputFilter);

        $this->add((new Text('title'))->setLabel('Title:')->setAttribute('id', 'title'));
        $this->add((new Textarea('content'))->setLabel('Content:')->setAttribute('id', 'content'));
        $this->add((new Url('url'))->setLabel('License url:')->setAttribute('id', 'url'));
        $this->add((new Url('iconHref'))->setLabel('Icon url:')->setAttribute('id', 'iconHref'));

        $this->add(
            (new Submit('submit'))->setValue('Save')->setAttribute('class', 'btn btn-success pull-right')
        );

        $inputFilter->add(
            [
                'name'     => 'title',
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StripTags'
                    ]
                ]
            ]
        );

        $inputFilter->add(
            [
                'name'     => 'content',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StripTags'
                    ]
                ]
            ]
        );

        $inputFilter->add(
            [
                'name'     => 'iconHref',
                'required' => false,
                'filters'  => [
                    [
                        'name' => 'StripTags'
                    ]
                ]
            ]
        );

        $inputFilter->add(
            [
                'name'     => 'url',
                'required' => true,
                'filters'  => [
                    [
                        'name' => 'StripTags'
                    ]
                ]
            ]
        );
    }
}