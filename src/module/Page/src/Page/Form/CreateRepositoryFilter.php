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
namespace Page\Form;

use Zend\InputFilter\InputFilter;

class CreateRepositoryFilter extends InputFilter
{
    use \Common\Traits\ObjectManagerAwareTrait;

    public function __construct ($objectManager)
    {        

       
        $this->add(array(
            'name' => 'slug',
            'required' => true,
            'validators' => array(
                array(
                    'name' => 'Page\Validator\UniqueRepository',
                    'options' => array(
                        'object_repository' => $objectManager->getRepository('Page\Entity\PageRepository'),
                        'fields' => array('slug'),
                        'object_manager' => $objectManager
                    )
                )
            )
        ));
     
    }
}