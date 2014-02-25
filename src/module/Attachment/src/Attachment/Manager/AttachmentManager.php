<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright   Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Attachment\Manager;

use Attachment\Entity\ContainerInterface;
use Attachment\Exception;
use Attachment\Form\AttachmentFieldsetProvider;
use Attachment\Options\ModuleOptions;
use Authorization\Service\AuthorizationAssertionTrait;
use ClassResolver\ClassResolverAwareTrait;
use ClassResolver\ClassResolverInterface;
use Common\Traits\ConfigAwareTrait;
use Common\Traits\ObjectManagerAwareTrait;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Persistence\ObjectManager;
use Instance\Manager\InstanceManagerAwareTrait;
use Instance\Manager\InstanceManagerInterface;
use Type\TypeManagerAwareTrait;
use Type\TypeManagerInterface;
use Uuid\Manager\UuidManagerAwareTrait;
use Zend\Filter\File\RenameUpload;
use ZfcRbac\Service\AuthorizationService;
use ZfcRbac\Service\AuthorizationServiceAwareTrait;

class AttachmentManager implements AttachmentManagerInterface
{
    use ClassResolverAwareTrait;
    use AuthorizationAssertionTrait, ObjectManagerAwareTrait;
    use InstanceManagerAwareTrait, TypeManagerAwareTrait;

    /**
     * @var \Attachment\Options\ModuleOptions
     */
    protected $moduleOptions;

    public function __construct(
        AuthorizationService $authorizationService,
        ClassResolverInterface $classResolver,
        InstanceManagerInterface $instanceManager,
        ModuleOptions $moduleOptions,
        TypeManagerInterface $typeManager,
        ObjectManager $objectManager
    ) {
        $this->authorizationService = $authorizationService;
        $this->classResolver        = $classResolver;
        $this->instanceManager      = $instanceManager;
        $this->typeManager          = $typeManager;
        $this->objectManager        = $objectManager;
        $this->moduleOptions        = $moduleOptions;
    }

    public function attach(AttachmentFieldsetProvider $form, $type = 'file', $appendId = null)
    {
        if (!$form->isValid()) {
            throw new Exception\RuntimeException($form->getMessages());
        }

        if ($appendId !== null) {
            $this->assertGranted('attachment.append');
        } else {
            $this->assertGranted('attachment.create');
        }

        $data = $form->getData()['attachment'];
        if (!isset($data['file']) || $data['file']['error']) {
            throw new Exception\NoFileSent;
        }

        $file      = $data['file'];
        $filename  = $file['name'];
        $size      = $file['size'];
        $filetype  = $file['type'];
        $pathinfo  = pathinfo($filename);
        $extension = isset($pathinfo['extension']) ? '.' . $pathinfo['extension'] : '';
        $hash      = uniqid() . '_' . hash('ripemd160', $filename) . $extension;
        $location    = $this->findParentPath($this->moduleOptions->getPath()) . '/' . $hash;
        $webLocation = $this->moduleOptions->getWebpath() . '/' . $hash;
        $filter      = new RenameUpload($location);
        $filter->filter($file);

        if ($appendId) {
            $attachment = $this->getAttachment($appendId);
        } else {
            $attachment = $this->createAttachment();
            $type       = $this->getTypeManager()->findTypeByName($type);
            $attachment->setType($type);
        }

        return $this->attachFile($attachment, $filename, $webLocation, $size, $filetype);
    }

    public function getFile($attachmentId, $fileId = null)
    {
        $attachment = $this->getAttachment($attachmentId);
        $this->assertGranted('attachment.get', $attachment);

        if ($fileId) {
            $criteria = Criteria::create()->where(Criteria::expr()->eq('id', $fileId));
            $matching = $attachment->getFiles()->matching($criteria);
            $file     = $matching->first();

            if (!is_object($file)) {
                throw new Exception\FileNotFoundException(sprintf('Container found, but file id does not exist.'));
            }

            return $file;
        } else {
            return $attachment->getFirstFile();
        }
    }

    public function getAttachment($id)
    {
        /* @var $entity \Attachment\Entity\ContainerInterface */
        $entity = $this->getObjectManager()->find(
            $this->getClassResolver()->resolveClassName('Attachment\Entity\ContainerInterface'),
            $id
        );

        if (!is_object($entity)) {
            throw new Exception\AttachmentNotFoundException(sprintf('Upload "%s" not found', $id));
        }

        return $entity;
    }

    /**
     * @param $path
     * @return bool|string
     */
    protected static function findParentPath($path)
    {
        $dir         = __DIR__;
        $previousDir = '.';
        while (!is_dir($dir . '/' . $path) && !file_exists($dir . '/' . $path)) {
            $dir = dirname($dir);
            if ($previousDir === $dir) {
                return false;
            }
            $previousDir = $dir;
        }

        return $dir . '/' . $path;
    }

    protected function createAttachment()
    {
        /* @var $attachment ContainerInterface */
        $attachment = $this->getClassResolver()->resolve('Attachment\Entity\ContainerInterface');
        $instance   = $this->getInstanceManager()->getInstanceFromRequest();

        $attachment->setInstance($instance);
        $this->getObjectManager()->persist($attachment);

        return $attachment;
    }

    protected function attachFile(ContainerInterface $attachment, $filename, $location, $size, $type)
    {
        $file = $this->getClassResolver()->resolve('Attachment\Entity\FileInterface');

        $file->setFilename($filename);
        $file->setLocation($location);
        $file->setSize($size);
        $file->setType($type);
        $file->setAttachment($attachment);
        $attachment->addFile($file);
        $this->getObjectManager()->persist($file);

        return $attachment;
    }

    public function flush()
    {
        $this->getObjectManager()->flush();
    }
}
