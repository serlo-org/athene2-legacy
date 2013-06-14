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
namespace Entity\Form\Revision;

class RevisionWithTitleAndContentFieldset extends AbstractRevisionFieldset
{
    function __construct ()
    {
        parent::__construct();
        
        $this->add(array(
            'name' => 'title',
            'type' => 'Text',
        ));
        
        $this->add(array(
            'name' => 'content',
            'type' => 'Zend\Form\Element\Textarea',
            'attributes' => array(
                'class' => 'ckeditor'
            )
        ));
    }
    
    public function getInputFilterSpecification(){
        return array(
            'title' => array(
                'required' => true,
            ),
            'content' => array(
                'required' => true,
            ),
        );
    }
}