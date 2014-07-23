<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Discussion\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Notification\Form\OptInFieldset;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Textarea;
use Zend\InputFilter\InputFilter;

class CommentForm extends AbstractForm
{

    function __construct(ObjectManager $objectManager)
    {
        parent::__construct('comment');
        $hydrator    = new DoctrineObject($objectManager);
        $inputFilter = new InputFilter('comment');

        $this->setHydrator($hydrator);
        $this->setInputFilter($inputFilter);
        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'clearfix');

        $this->add(
            [
                'type'    => 'Common\Form\Element\ObjectHidden',
                'name'    => 'author',
                'options' => [
                    'object_manager' => $objectManager,
                    'target_class'   => 'User\Entity\User'
                ]
            ]
        );
        $this->add(
            [
                'type'    => 'Common\Form\Element\ObjectHidden',
                'name'    => 'parent',
                'options' => [
                    'object_manager' => $objectManager,
                    'target_class'   => 'Discussion\Entity\Comment'
                ]
            ]
        );
        $this->add(
            [
                'type'    => 'Common\Form\Element\ObjectHidden',
                'name'    => 'instance',
                'options' => [
                    'object_manager' => $objectManager,
                    'target_class'   => 'Instance\Entity\Instance'
                ]
            ]
        );
        $this->add((new Textarea('content'))->setAttribute('placeholder', 'Content'));
        $this->add(new OptInFieldset());
        $this->add(
            (new Submit('start'))->setValue('Reply')->setAttribute('class', 'btn btn-success pull-right')
        );

        $inputFilter->add(['name' => 'content', 'required' => true, 'filters' => [['name' => 'StripTags']]]);
        $inputFilter->add(['name' => 'instance', 'required' => true]);
        $inputFilter->add(['name' => 'author', 'required' => true]);
        $inputFilter->add(['name' => 'parent', 'required' => true]);
    }
}
