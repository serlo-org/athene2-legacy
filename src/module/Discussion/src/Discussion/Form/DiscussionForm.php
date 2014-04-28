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

use Common\Hydrator\HydratorPluginAwareDoctrineObject;
use Doctrine\Common\Persistence\ObjectManager;
use Notification\Form\OptInFieldset;
use Zend\Form\Element\Hidden;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Element\Textarea;
use Zend\InputFilter\InputFilter;

class DiscussionForm extends AbstractForm
{

    function __construct(HydratorPluginAwareDoctrineObject $hydrator, ObjectManager $objectManager)
    {
        parent::__construct('discussion');
        $inputFilter = new InputFilter('discussion');

        $this->setInputFilter($inputFilter);
        $this->setHydrator($hydrator);
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
                'name'    => 'object',
                'options' => [
                    'object_manager' => $objectManager,
                    'target_class'   => 'Uuid\Entity\Uuid'
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
        $this->add(new Hidden('terms'));
        $this->add((new Text('title'))->setLabel('Title:'));
        $this->add((new Textarea('content'))->setLabel('content:'));
        $this->add(new OptInFieldset());
        $this->add(
            (new Submit('submit'))->setValue('Start discussion')->setAttribute('class', 'btn btn-success pull-right')
        );

        $inputFilter->add(
            [
                'name'       => 'title',
                'required'   => true,
                'filters'    => [['name' => 'StripTags']],
                'validators' => [
                    [
                        'name'    => 'Regex',
                        'options' => ['pattern' => '~^[a-zA-Z\-_ /0-9äöüÄÖÜ?!.,]*$~']
                    ]
                ]
            ]
        );
        $inputFilter->add(['name' => 'instance', 'required' => true]);
        $inputFilter->add(['name' => 'content', 'required' => true, 'filters' => [['name' => 'StripTags']]]);
    }
}
