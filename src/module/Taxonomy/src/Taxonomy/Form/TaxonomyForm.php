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
namespace Taxonomy\Form;

use Zend\Form\Form;

class TaxonomyForm extends Form
{
    function __construct ()
    {
        parent::__construct('taxonomy');
        $this->setAttribute('method', 'post');
        $this->add(new TermFieldset());

        $this->add(array(
            'name' => 'id',
            'label' => '',
            'attributes' => array(
                'type' => 'hidden'
            ),
            'options' => array()
        ));
        

        $this->add(array(
            'name' => 'parent',
            'label' => '',
            'attributes' => array(
                'type' => 'hidden'
            ),
            'options' => array()
        ));
        
        
        $this->add(array(
            'name' => 'taxonomy',
            'label' => '',
            'attributes' => array(
                'type' => 'hidden'
            ),
            'options' => array()
        ));
        
        $this->add(array(
            'name' => 'weight',
            'label' => '',
            'attributes' => array(
                'type' => 'hidden'
            ),
            'options' => array()
        ));
            

        $this->add(array(
            'name' => 'submit',
            'label' => '',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Speichern',
                'class' => 'btn btn-success'
            ),
            'options' => array()
        ));
        
        $this->add(array(
            'name' => 'reset',
            'label' => '',
            'attributes' => array(
                'type' => 'reset',
                'value' => 'Verwerfen',
                'class' => 'btn',
            ),
            'options' => array(
            )
        ));
    }
}