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
namespace Authorization\Assertion;

use Authorization\Exception\InvalidArgumentException;
use Authorization\Result\AuthorizationResult;
use Authorization\Service\PermissionServiceInterface;
use Instance\Entity\InstanceInterface;
use Instance\Entity\InstanceProviderInterface;
use Instance\Manager\InstanceManagerInterface;
use Rbac\Traversal\Strategy\TraversalStrategyInterface;

class InstanceAssertion implements AssertionInterface
{
    /**
     * @var PermissionServiceInterface
     */
    protected $permissionService;

    /**
     * @var InstanceManagerInterface
     */
    protected $instanceManager;

    /**
     * @var TraversalStrategyInterface
     */
    protected $traversalStrategy;

    /**
     * @param InstanceManagerInterface   $instanceManager
     * @param PermissionServiceInterface $permissionService
     * @param TraversalStrategyInterface $traversalStrategy
     */
    public function __construct(
        InstanceManagerInterface $instanceManager,
        PermissionServiceInterface $permissionService,
        TraversalStrategyInterface $traversalStrategy
    ) {
        $this->permissionService = $permissionService;
        $this->instanceManager   = $instanceManager;
        $this->traversalStrategy = $traversalStrategy;
    }

    /**
     * Check if this assertion is true
     *
     * @param  AuthorizationResult $authorization
     * @param  mixed               $context
     * @throws InvalidArgumentException
     * @return bool
     */
    public function assert(AuthorizationResult $authorization, $context = null)
    {
        if ($context === null) {
            $instance = null;
        } elseif ($context instanceof InstanceProviderInterface) {
            $instance = $context->getInstance()->getId();
        } elseif ($context instanceof InstanceInterface) {
            $instance = $context->getId();
        } else {
            throw new InvalidArgumentException;
        }

        $permissionToCheck = $authorization->getPermission();
        $rolesToCheck      = $this->flattenRoles($authorization->getRoles());

        $permissions = $this->permissionService->findParametrizedPermissions(
            $permissionToCheck,
            'instance',
            $instance
        );

        if($this->isGranted($rolesToCheck, $permissions)){
            return true;
        }

        // Not found..well, that's unfortunate! Let's check if it's satisfiable with global permissions
        if ($instance !== null) {
            $permissions = $this->permissionService->findParametrizedPermissions(
                $permissionToCheck,
                'instance',
                null
            );

            if($this->isGranted($rolesToCheck, $permissions)){
                return true;
            }
        }

        return false;
    }

    /**
     * Flatten an array of role with role names
     * This method iterates through the list of roles, and convert any RoleInterface to a string. For any
     * role, it also extracts all the children
     *
     * @param  array|RoleInterface[] $roles
     * @return RoleInterface[]
     */
    public function flattenRoles(array $roles)
    {
        $roleNames = [];
        $iterator  = $this->traversalStrategy->getRolesIterator($roles);

        foreach ($iterator as $role) {
            $roleNames[] = $role;
        }

        return array_unique($roleNames);
    }

    /**
     * @param array $roles
     * @param array $permissions
     * @return bool
     */
    protected function isGranted($roles, $permissions)
    {
        foreach ($permissions as $permission) {
            foreach ($roles as $role) {
                if ($role->hasPermission($permission)) {
                    return true;
                }
            }
        }

        return false;
    }
}
