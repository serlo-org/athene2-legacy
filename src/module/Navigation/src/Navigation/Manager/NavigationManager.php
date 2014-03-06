<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Navigation\Manager;

use Authorization\Service\AuthorizationAssertionTrait;
use Authorization\Service\AuthorizationService;
use ClassResolver\ClassResolverInterface;
use Common\Traits\FlushableTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Instance\Entity\InstanceInterface;
use Instance\Manager\InstanceManagerInterface;
use Navigation\Entity\ContainerInterface;
use Navigation\Entity\PageInterface;
use Navigation\Entity\ParameterInterface;
use Navigation\Entity\ParameterKeyInterface;
use Navigation\Exception\ContainerNotFoundException;
use Navigation\Exception\PageNotFoundException;
use Navigation\Exception\ParameterNotFoundException;
use Navigation\Exception\RuntimeException;
use Type\Entity\TypeInterface;
use Type\TypeManagerInterface;
use Zend\Form\FormInterface;

class NavigationManager implements NavigationManagerInterface
{
    use AuthorizationAssertionTrait;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var TypeManagerInterface
     */
    protected $typeManager;

    /**
     * @var InstanceManagerInterface
     */
    protected $instanceManager;

    /**
     * @var ClassResolverInterface
     */
    protected $classResolver;

    /**
     * @var array
     */
    protected $interfaces = [
        'container' => 'Navigation\Entity\ContainerInterface',
        'page'      => 'Navigation\Entity\PageInterface',
        'parameter' => 'Navigation\Entity\ParameterInterface',
        'key'       => 'Navigation\Entity\ParameterKeyInterface',
    ];

    /**
     * @var array
     */
    protected $types = [
        'default',
        'footer',
        'top-center',
        'top-left'
    ];

    public function __construct(
        AuthorizationService $authorizationService,
        ClassResolverInterface $classResolver,
        InstanceManagerInterface $instanceManager,
        ObjectManager $objectManager,
        TypeManagerInterface $typeManager
    ) {
        $this->objectManager        = $objectManager;
        $this->typeManager          = $typeManager;
        $this->instanceManager      = $instanceManager;
        $this->classResolver        = $classResolver;
        $this->authorizationService = $authorizationService;
    }

    /**
     * @param FormInterface $form
     * @return ContainerInterface
     */
    public function createContainer(FormInterface $form)
    {
        /* @var $entity FormInterface */
        $entity = $this->classResolver->resolve($this->interfaces['container']);

        return $this->bind($entity, $form);
    }

    /**
     * @param FormInterface $form
     * @return PageInterface
     */
    public function createPage(FormInterface $form)
    {
        /* @var $entity PageInterface */
        $entity = $this->classResolver->resolve($this->interfaces['page']);

        return $this->bind($entity, $form);
    }

    /**
     * @param FormInterface $form
     * @return ParameterInterface
     */
    public function createParameter(FormInterface $form)
    {
        /* @var $entity ParameterInterface */
        $entity = $this->classResolver->resolve($this->interfaces['parameter']);

        return $this->bind($entity, $form);
    }

    /**
     * @param FormInterface $form
     * @return ParameterKeyInterface
     */
    public function createParameterKey(FormInterface $form)
    {
        /* @var $entity ParameterKeyInterface */
        $entity = $this->classResolver->resolve($this->interfaces['key']);

        return $this->bind($entity, $form);
    }

    /**
     * @return void
     */
    public function flush()
    {
        $this->objectManager->flush();
    }

    /**
     * @param InstanceInterface $instance
     * @return ContainerInterface[]
     */
    public function findContainersByInstance(InstanceInterface $instance)
    {
        $className  = $this->classResolver->resolveClassName($this->interfaces['container']);
        $repository = $this->objectManager->getRepository($className);

        return $repository->findBy(
            [
                'instance' => $instance->getId()
            ]
        );
    }

    /**
     * @param string            $name
     * @param InstanceInterface $instance
     * @throws ContainerNotFoundException
     * @return ContainerInterface
     */
    public function findContainerByNameAndInstance($name, InstanceInterface $instance)
    {
        $className  = $this->classResolver->resolveClassName($this->interfaces['container']);
        $repository = $this->objectManager->getRepository($className);
        $type       = $this->typeManager->findTypeByName($name);
        $container  = $repository->findOneBy(
            [
                'type'     => $type->getId(),
                'instance' => $instance->getId()
            ]
        );

        if (!is_object($container)) {
            throw new ContainerNotFoundException(sprintf("Container %s, %s not found", $type, $instance));
        }

        return $container;
    }

    /**
     * @param int $id
     * @return ContainerInterface
     * @throws ContainerNotFoundException
     */
    public function getContainer($id)
    {
        $className = $this->classResolver->resolveClassName($this->interfaces['container']);
        $container = $this->objectManager->find($className, $id);

        if (!is_object($container)) {
            throw new ContainerNotFoundException(sprintf("Container %s not found", $id));
        }

        $this->assertGranted('navigation.manage', $container);

        return $container;
    }

    /**
     * @param int $id
     * @return PageInterface
     * @throws PageNotFoundException
     */
    public function getPage($id)
    {
        $className = $this->classResolver->resolveClassName($this->interfaces['page']);
        $page = $this->objectManager->find($className, $id);

        if (!is_object($page)) {
            throw new PageNotFoundException(sprintf("Container %s not found", $id));
        }
        $this->assertGranted('navigation.manage', $page);

        return $page;
    }

    /**
     * @param int $id
     * @return ParameterInterface
     * @throws ParameterNotFoundException
     */
    public function getParameter($id)
    {
        $className = $this->classResolver->resolveClassName($this->interfaces['parameter']);
        $parameter = $this->objectManager->find($className, $id);

        if (!is_object($parameter)) {
            throw new ParameterNotFoundException(sprintf("Container %s not found", $id));
        }

        $this->assertGranted('navigation.manage', $parameter);

        return $parameter;
    }

    /**
     * @return ParameterKeyInterface[]
     */
    public function getParameterKeys()
    {
        $className = $this->classResolver->resolveClassName($this->interfaces['key']);
        return $this->objectManager->getRepository($className)->findAll();
    }

    /**
     * @return TypeInterface[]
     */
    public function getTypes()
    {
        $return = [];
        $types  = $this->typeManager->findAllTypes();

        foreach ($types as $type) {
            if (in_array($type->getName(), $this->types)) {
                $return[] = $type;
            }
        }

        return $return;
    }

    /**
     * @param int $id
     * @throws ContainerNotFoundException
     * @return void
     */
    public function removeContainer($id)
    {
        $container = $this->getContainer($id);

        $this->assertGranted('navigation.manage', $container);
        $this->objectManager->remove($container);
    }

    /**
     * @param int $id
     * @return void
     */
    public function removePage($id)
    {
        $page = $this->getPage($id);

        $this->assertGranted('navigation.manage', $page);
        $this->objectManager->remove($page);
    }

    /**
     * @param int $id
     * @return void
     */
    public function removeParameter($id)
    {
        $parameter = $this->getParameter($id);

        $this->assertGranted('navigation.manage', $parameter);
        $this->objectManager->remove($parameter);
    }

    /**
     * @param FormInterface $form
     * @return void
     * @throws RuntimeException
     */
    public function updatePage(FormInterface $form)
    {
        $object = $form->getObject();

        $this->assertGranted('navigation.manage', $object);

        if (!$form->isValid()) {
            throw new RuntimeException;
        }

        $this->objectManager->persist($object);
    }

    /**
     * @param FormInterface $form
     * @return void
     * @throws RuntimeException
     */
    public function updateParameter(FormInterface $form)
    {
        $object = $form->getObject();

        $this->assertGranted('navigation.manage', $object);

        if (!$form->isValid()) {
            throw new RuntimeException;
        }

        $this->objectManager->persist($object);
    }

    /**
     * @param object        $object
     * @param FormInterface $form
     * @return object
     * @throws RuntimeException
     */
    protected function bind($object, FormInterface $form)
    {
        $processingForm = clone $form;

        $data = $processingForm->getData(FormInterface::VALUES_AS_ARRAY);
        $processingForm->bind($object);
        $processingForm->setData($data);

        if (!$processingForm->isValid()) {
            throw new RuntimeException;
        }

        if ($object instanceof ParameterKeyInterface) {
            $instance = $this->instanceManager->getInstanceFromRequest();
        } else {
            $instance = $object;
        }

        $this->assertGranted('navigation.manage', $instance);

        $this->objectManager->persist($object);

        // clear form
        $processingForm->setObject(null);
        $processingForm->setData([]);

        return $object;
    }
}
