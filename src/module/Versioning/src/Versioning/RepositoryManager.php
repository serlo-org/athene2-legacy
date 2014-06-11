<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Versioning;

use Authorization\Service\AuthorizationAssertionTrait;
use Common\Traits\InstanceManagerTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Versioning\Entity\RepositoryInterface;
use Versioning\Entity\RevisionInterface;
use Versioning\Options\ModuleOptions;
use Zend\EventManager\EventManagerAwareTrait;
use ZfcRbac\Service\AuthorizationService;

class RepositoryManager implements RepositoryManagerInterface
{
    use EventManagerAwareTrait, AuthorizationAssertionTrait;

    /**
     * @var ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var RepositoryInterface
     */
    protected $repository;

    /**
     * @var ObjectManager
     */
    protected $objectManager;

    /**
     * @var AuthorizationService
     */
    protected $authorizationService;

    /**
     * @param AuthorizationService $authorizationService
     * @param ModuleOptions        $moduleOptions
     * @param ObjectManager        $objectManager
     */
    public function __construct(
        AuthorizationService $authorizationService,
        ModuleOptions $moduleOptions,
        ObjectManager $objectManager
    ) {
        $this->moduleOptions        = $moduleOptions;
        $this->objectManager        = $objectManager;
        $this->authorizationService = $authorizationService;
    }

    /**
     * {@inheritDoc}
     */
    public function checkoutRevision(RepositoryInterface $repository, $revision, $reason = '')
    {
        if (!$revision instanceof RevisionInterface) {
            $revision = $this->findRevision($repository, $revision);
        }

        $user       = $this->getAuthorizationService()->getIdentity();
        $permission = $this->moduleOptions->getPermission($repository, 'checkout');
        $this->assertGranted($permission, $repository);
        $repository->setCurrentRevision($revision);

        $this->getEventManager()->trigger(
            'checkout',
            $this,
            [
                'repository' => $repository,
                'revision'   => $revision,
                'actor'      => $user,
                'reason'     => $reason
            ]
        );

        $this->objectManager->persist($repository);
    }

    /**
     * {@inheritDoc}
     */
    public function commitRevision(RepositoryInterface $repository, array $data)
    {
        $user       = $this->getAuthorizationService()->getIdentity();
        $permission = $this->moduleOptions->getPermission($repository, 'commit');
        $revision   = $repository->createRevision();

        $this->assertGranted($permission, $repository);
        $revision->setAuthor($user);
        $repository->addRevision($revision);
        $revision->setRepository($repository);

        foreach ($data as $key => $value) {
            if (is_string($key) && is_string($value)) {
                $revision->set($key, $value);
            }
        }

        $this->getEventManager()->trigger(
            'commit',
            $this,
            [
                'repository' => $repository,
                'revision'   => $revision,
                'data'       => $data,
                'author'     => $user
            ]
        );

        $this->objectManager->persist($revision);

        return $revision;
    }

    /**
     * {@inheritDoc}
     */
    public function findRevision(RepositoryInterface $repository, $id)
    {
        foreach ($repository->getRevisions() as $revision) {
            if ($revision->getId() == $id) {
                return $revision;
            }
        }

        throw new Exception\RevisionNotFoundException(sprintf('Revision "%d" not found', $id));
    }

    /**
     * {@inheritDoc}
     */
    public function flush()
    {
        $this->objectManager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function rejectRevision(RepositoryInterface $repository, $revision, $reason = '')
    {
        if (!$revision instanceof RevisionInterface) {
            $revision = $this->findRevision($repository, $revision);
        }

        $user       = $this->getAuthorizationService()->getIdentity();
        $permission = $this->moduleOptions->getPermission($repository, 'reject');

        $this->assertGranted($permission, $repository);
        $revision->setTrashed(true);
        $this->objectManager->persist($revision);
        $this->getEventManager()->trigger(
            'reject',
            $this,
            [
                'repository' => $repository,
                'revision'   => $revision,
                'actor'      => $user,
                'reason'     => $reason
            ]
        );
    }
}
