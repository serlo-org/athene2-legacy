<?php

/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Jakob Pfab (jakob.pfab@serlo.org)
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Page\Manager;

use Authorization\Service\AuthorizationAssertionTrait;
use Authorization\Service\RoleServiceAwareTrait;
use Authorization\Service\RoleServiceInterface;
use ClassResolver\ClassResolverAwareTrait;
use ClassResolver\ClassResolverInterface;
use Common\Traits\FlushableTrait;
use Common\Traits\ObjectManagerAwareTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Instance\Entity\InstanceInterface;
use Instance\Manager\InstanceManagerAwareTrait;
use Instance\Manager\InstanceManagerInterface;
use License\Manager\LicenseManagerAwareTrait;
use License\Manager\LicenseManagerInterface;
use Page\Entity\PageRepositoryInterface;
use Page\Exception\InvalidArgumentException;
use Page\Exception\PageNotFoundException;
use Page\Exception\RuntimeException;
use User\Entity\UserInterface;
use User\Manager\UserManagerAwareTrait;
use User\Manager\UserManagerInterface;
use Uuid\Manager\UuidManagerAwareTrait;
use Versioning\RepositoryManagerAwareTrait;
use Versioning\RepositoryManagerInterface;
use Zend\Form\FormInterface;
use ZfcRbac\Service\AuthorizationService;

class PageManager implements PageManagerInterface
{
    use ObjectManagerAwareTrait, ClassResolverAwareTrait;
    use InstanceManagerAwareTrait, AuthorizationAssertionTrait;
    use LicenseManagerAwareTrait, RepositoryManagerAwareTrait;
    use RoleServiceAwareTrait, UserManagerAwareTrait;
    use FlushableTrait;

    public function __construct(
        AuthorizationService $authorizationService,
        ClassResolverInterface $classResolver,
        InstanceManagerInterface $instanceManager,
        LicenseManagerInterface $licenseManager,
        ObjectManager $objectManager,
        RepositoryManagerInterface $repositoryManager,
        RoleServiceInterface $roleService,
        UserManagerInterface $userManager
    ) {
        $this->setAuthorizationService($authorizationService);
        $this->classResolver     = $classResolver;
        $this->instanceManager   = $instanceManager;
        $this->licenseManager    = $licenseManager;
        $this->objectManager     = $objectManager;
        $this->repositoryManager = $repositoryManager;
        $this->roleService       = $roleService;
        $this->userManager       = $userManager;
    }

    public function createPageRepository(FormInterface $form)
    {
        $formClone = clone $form;
        if (!$formClone->isValid()) {
            throw new RuntimeException(print_r($formClone->getMessages(), true));
        }
        $data   = $formClone->getData(FormInterface::VALUES_AS_ARRAY);
        $entity = $formClone->getObject();
        $formClone->setData($data);
        $formClone->isValid();
        $this->assertGranted('page.create', $entity);
        $this->getObjectManager()->persist($entity);
        return $entity;
    }

    public function createRevision(PageRepositoryInterface $repository, array $data, UserInterface $user)
    {
        $this->assertGranted('page.revision.create', $repository);

        $repository = $this->getRepositoryManager()->getRepository($repository);
        $revision   = $repository->commitRevision($data);
        $this->getObjectManager()->persist($revision);
        $this->getObjectManager()->persist($repository->getRepository());
        $repository->checkoutRevision($revision->getId());

        return $repository;
    }

    public function editPageRepository(FormInterface $form)
    {
        $page = $form->getObject();
        if (!$form->isValid()) {
            throw new RuntimeException(print_r($form->getMessages(), true));
        }
        $data      = $form->getData(FormInterface::VALUES_AS_ARRAY);
        $formClone = clone $form;
        $formClone->bind($page);
        $formClone->setData($data);
        $formClone->isValid();
        $this->assertGranted('page.update', $page);
        $this->getObjectManager()->persist($page);
        return $page;
    }

    public function findAllRepositories(InstanceInterface $instance)
    {
        $this->assertGranted('page.get', $instance);
        $className    = $this->getClassResolver()->resolveClassName('Page\Entity\PageRepositoryInterface');
        $params       = ['instance' => $instance->getId(), 'trashed' => false];
        $repositories = $this->getObjectManager()->getRepository($className)->findBy($params);
        return $repositories;
    }

    public function findAllRoles()
    {
        return $this->getRoleService()->findAllRoles();
    }

    public function getPageRepository($id)
    {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException(sprintf('Expected numeric but got %s', gettype($id)));
        }

        $className      = $this->getClassResolver()->resolveClassName('Page\Entity\PageRepositoryInterface');
        $pageRepository = $this->getObjectManager()->find($className, $id);

        if (!is_object($pageRepository)) {
            throw new PageNotFoundException(sprintf('Page Repository "%d" not found.', $id));
        } elseif ($pageRepository->isTrashed()) {
            throw new PageNotFoundException(sprintf('Page Repository "%d" is trashed.', $id));
        }

        $this->assertGranted('page.get', $pageRepository);

        return $pageRepository;
    }

    public function getRevision($id)
    {
        if (!is_numeric($id)) {
            throw new InvalidArgumentException(sprintf('Expected numeric but got %s', gettype($id)));
        }

        $className = $this->getClassResolver()->resolveClassName('Page\Entity\PageRevisionInterface');
        $revision  = $this->getObjectManager()->find($className, $id);
        $this->assertGranted('page.get', $revision);

        if (!$revision) {
            throw new PageNotFoundException(sprintf('Page Revision %s not found', $id));
        } else {
            if ($revision->isTrashed()) {
                throw new PageNotFoundException(sprintf('Page Revision %s is trashed', $id));
            }
        }

        return $revision;
    }

    protected function countRoles()
    {
        $roles = $this->findAllRoles();
        return count($roles);
    }

    protected function setRoles(array $data, PageRepositoryInterface $pageRepository)
    {
        $pageRepository->setRoles(new ArrayCollection());
        for ($i = 0; $i <= $this->countRoles(); $i++) {
            if (array_key_exists($i, $data['roles'])) {
                $role = $this->getRoleService()->getRole($data['roles'][$i]);
                if ($role != null) {
                    $pageRepository->addRole($role);
                }
            }
        }
    }
}
