<?php


namespace Page\Form;

use Doctrine\Common\Persistence\ObjectManager;
use DoctrineModule\Stdlib\Hydrator\DoctrineObject;
use Zend\Form\Element\Submit;
use Zend\Form\Element\Text;
use Zend\Form\Form;
use Zend\InputFilter\InputFilter;

class RepositoryForm extends Form
{

    public function __construct(ObjectManager $objectManager)
    {
        parent::__construct('createPage');

        // Hydration does not work with byValue (why?)
        $hydrator = new DoctrineObject($objectManager, false);
        $filter   = new InputFilter();

        $this->setAttribute('method', 'post');
        $this->setAttribute('class', 'form-horizontal');
        $this->setInputFilter($filter);
        $this->setHydrator($hydrator);

        $this->add((new Text('slug'))->setLabel('Url:'));
        $this->add((new Text('forum'))->setLabel('Forum Id:')->setAttribute('placeholder', '123'));

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

        $this->add(
            array(
                'type'    => 'DoctrineModule\Form\Element\ObjectSelect',
                'name'    => 'license',
                'options' => array(
                    'object_manager' => $objectManager,
                    'label'          => 'License',
                    'target_class'   => 'License\Entity\License',
                    'property'       => 'title'
                )
            )
        );

        $this->add(
            array(
                'type'    => 'DoctrineModule\Form\Element\ObjectMultiCheckbox',
                'name'    => 'roles',
                'options' => array(
                    'object_manager' => $objectManager,
                    'label'          => 'Roles',
                    'target_class'   => 'User\Entity\Role',
                )
            )
        );

        $this->add((new Submit('submit'))->setValue('Save')->setAttribute('class', 'btn btn-success pull-right'));
    }
}