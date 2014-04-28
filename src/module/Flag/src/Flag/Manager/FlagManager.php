<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Flag\Manager;

use Authorization\Service\AuthorizationAssertionTrait;
use ClassResolver\ClassResolverAwareTrait;
use ClassResolver\ClassResolverInterface;
use Common\Traits\FlushableTrait;
use Common\Traits\ObjectManagerAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Flag\Exception;
use Flag\Options\ModuleOptions;
use Instance\Manager\InstanceManagerAwareTrait;
use Instance\Manager\InstanceManagerInterface;
use Type\TypeManagerAwareTrait;
use Type\TypeManagerInterface;
use User\Manager\UserManagerAwareTrait;
use User\Manager\UserManagerInterface;
use Uuid\Manager\UuidManagerAwareTrait;
use Uuid\Manager\UuidManagerInterface;
use ZfcRbac\Service\AuthorizationService;

class FlagManager implements FlagManagerInterface
{
    use ObjectManagerAwareTrait, UuidManagerAwareTrait;
    use ClassResolverAwareTrait, TypeManagerAwareTrait;
    use InstanceManagerAwareTrait, AuthorizationAssertionTrait;
    use UserManagerAwareTrait, FlushableTrait;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @param AuthorizationService     $authorizationService
     * @param ClassResolverInterface   $classResolver
     * @param InstanceManagerInterface $instanceManager
     * @param ObjectManager            $objectManager
     * @param TypeManagerInterface     $typeManager
     * @param UserManagerInterface     $userManager
     * @param UuidManagerInterface     $uuidManager
     */
    public function __construct(
        AuthorizationService $authorizationService,
        ClassResolverInterface $classResolver,
        InstanceManagerInterface $instanceManager,
        ModuleOptions $moduleOptions,
        ObjectManager $objectManager,
        TypeManagerInterface $typeManager,
        UserManagerInterface $userManager,
        UuidManagerInterface $uuidManager
    ) {
        $this->setAuthorizationService($authorizationService);
        $this->classResolver   = $classResolver;
        $this->instanceManager = $instanceManager;
        $this->objectManager   = $objectManager;
        $this->typeManager     = $typeManager;
        $this->userManager     = $userManager;
        $this->uuidManager     = $uuidManager;
        $this->moduleOptions   = $moduleOptions;
    }

    public function findAllTypes()
    {
        $collection = new ArrayCollection();
        foreach ($this->moduleOptions->getTypes() as $type) {
            $collection->add(
                $this->getTypeManager()->findTypeByName($type)
            );
        }

        return $collection;
    }

    public function findAllFlags()
    {
        $className = $this->getClassResolver()->resolveClassName('Flag\Entity\FlagInterface');
        $flags     = $this->getObjectManager()->getRepository($className)->findAll();

        foreach ($flags as $flag) {
            $this->assertGranted('flag.get', $flag);
        }

        return new ArrayCollection($flags);
    }

    public function removeFlag($id)
    {
        $flag = $this->getFlag($id);
        $this->assertGranted('flag.remove', $flag);
        $this->getObjectManager()->remove($flag);
    }

    public function getFlag($id)
    {
        $className = $this->getClassResolver()->resolveClassName('Flag\Entity\FlagInterface');
        $flag      = $this->getObjectManager()->find($className, $id);

        if (!is_object($flag)) {
            throw new Exception\FlagNotFoundException(sprintf('Flag not found by id %d', $id));
        }

        $this->assertGranted('flag.get', $flag);
        return $flag;
    }

    public function addFlag($typeId, $content, $uuid)
    {
        $type     = $this->getType($typeId);
        $object   = $this->getUuidManager()->getUuid($uuid);
        $reporter = $this->getUserManager()->getUserFromAuthenticator();
        $instance = $this->getInstanceManager()->getInstanceFromRequest();
        $this->assertGranted('flag.create', $instance);

        /* @var $flag \Flag\Entity\FlagInterface */
        $flag = $this->getClassResolver()->resolve('Flag\Entity\FlagInterface');
        $flag->setContent($content);
        $flag->setReporter($reporter);
        $flag->setType($type);
        $flag->setInstance($instance);
        $flag->setObject($object);
        $this->getObjectManager()->persist($flag);

        return $flag;
    }

    public function getType($id)
    {
        return $this->getTypeManager()->getType($id);
    }
}
