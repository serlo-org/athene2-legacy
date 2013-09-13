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

use Zend\Form\Fieldset;

class TermFieldset extends Fieldset
{

    function __construct ()
    {
        parent::__construct('term');
        
        $this->add(array(
            'name' => 'name',
            'attributes' => array(
                'type' => 'text',
                'id' => 'name'
            ),
            'options' => array(
                'placeholder' => 'Namen eingeben'
            )
        ));
    }
    
    public function getInputFilterSpecification(){
        return array(
            'name' => array(
                'required' => true,
            ),
        );
    }
}