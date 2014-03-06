<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Blog\Manager;

use Authorization\Service\AuthorizationAssertionTrait;
use Blog\Entity\PostInterface;
use Blog\Exception;
use ClassResolver\ClassResolverAwareTrait;
use ClassResolver\ClassResolverInterface;
use Common\Traits\ObjectManagerAwareTrait;
use Doctrine\Common\Persistence\ObjectManager;
use Instance\Entity\InstanceInterface;
use Instance\Manager\InstanceManagerAwareTrait;
use Instance\Manager\InstanceManagerInterface;
use Taxonomy\Manager\TaxonomyManagerAwareTrait;
use Taxonomy\Manager\TaxonomyManagerInterface;
use Uuid\Manager\UuidManagerAwareTrait;
use Zend\EventManager\EventManagerAwareTrait;
use Zend\Form\FormInterface;
use ZfcRbac\Service\AuthorizationService;

class BlogManager implements BlogManagerInterface
{
    use TaxonomyManagerAwareTrait, ObjectManagerAwareTrait;
    use ClassResolverAwareTrait;
    use InstanceManagerAwareTrait, AuthorizationAssertionTrait;
    use EventManagerAwareTrait;

    public function __construct(
        ClassResolverInterface $classResolver,
        TaxonomyManagerInterface $taxonomyManager,
        ObjectManager $objectManager,
        InstanceManagerInterface $instanceManager,
        AuthorizationService $authorizationService
    ) {
        $this->classResolver   = $classResolver;
        $this->taxonomyManager = $taxonomyManager;
        $this->instanceManager = $instanceManager;
        $this->objectManager   = $objectManager;
        $this->setAuthorizationService($authorizationService);
    }

    public function getBlog($id)
    {
        $blog = $this->getTaxonomyManager()->getTerm($id);
        $this->assertGranted('blog.get', $blog);
        return $blog;
    }

    public function findAllBlogs(InstanceInterface $instanceService)
    {
        $taxonomy = $this->getTaxonomyManager()->findTaxonomyByName('blog', $instanceService);
        foreach($taxonomy->getChildren() as $child){
            $this->assertGranted('blog.get', $child);
        }
        return $taxonomy->getChildren();
    }

    public function getPost($id)
    {
        $className = $this->getClassResolver()->resolveClassName('Blog\Entity\PostInterface');
        $post      = $this->getObjectManager()->find($className, $id);
        $this->assertGranted('blog.post.get', $post);

        if (!is_object($post)) {
            throw new Exception\PostNotFoundException(sprintf('Could not find post "%d"', $id));
        }

        return $post;
    }

    public function updatePost(FormInterface $form)
    {
        $post = $form->getObject();
        $this->assertGranted('blog.post.update', $post);
        $this->bind($post, $form);
        $this->objectManager->persist($post);
        $this->getEventManager()->trigger('update', $this, ['post' => $post]);
    }

    public function createPost(FormInterface $form)
    {
        $post = $this->getClassResolver()->resolve('Blog\Entity\PostInterface');
        $this->bind($post, $form);
        $this->assertGranted('blog.post.create', $post);
        $this->getObjectManager()->persist($post);
        $this->getEventManager()->trigger('create', $this, ['post' => $post]);
        return $post;
    }

    public function flush()
    {
        $this->getObjectManager()->flush();
    }

    protected function bind(PostInterface $comment, FormInterface $form)
    {
        if (!$form->isValid()) {
            throw new Exception\RuntimeException(print_r($form->getMessages(), true));
        }
        $data        = $form->getData(FormInterface::VALUES_AS_ARRAY);
        $processForm = clone $form;
        $processForm->bind($comment);
        $processForm->setData($data);
        if (!$processForm->isValid()) {
            throw new Exception\RuntimeException(print_r($processForm->getMessages(), true));
        }
        return $comment;
    }
}
