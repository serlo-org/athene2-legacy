<?php
/**
 * 
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author	Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license	LGPL-3.0
 * @license	http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link		https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace LearningResource\Plugin\Repository;

use LearningResource\Exception;
use Doctrine\Common\Collections\Criteria;
use Entity\Plugin\AbstractPlugin;
use Zend\Form\Form;
use User\Service\UserServiceInterface;
use Zend\Mvc\Router\RouteInterface;

class RepositoryPlugin extends AbstractPlugin
{
    use \Common\Traits\ObjectManagerAwareTrait,\Versioning\RepositoryManagerAwareTrait,\Common\Traits\AuthenticationServiceAwareTrait,\User\Manager\UserManagerAwareTrait,\Uuid\Manager\UuidManagerAwareTrait;

    /**
     *
     * @var RouteInterface
     */
    protected $router;

    /**
     *
     * @return \Zend\Mvc\Router\RouteInterface $router
     */
    public function getRouter()
    {
        return $this->router;
    }

    /**
     *
     * @param \Zend\Mvc\Router\RouteInterface $router            
     * @return $this
     */
    public function setRouter(RouteInterface $router)
    {
        $this->router = $router;
        return $this;
    }

    protected function getDefaultConfig()
    {
        return array(
            'revision_form' => 'FormNotFound',
            'fields' => array()
        );
    }

    /**
     *
     * @return RepositoryServiceInterface
     */
    public function getRepository()
    {
        $repository = $this->getEntityService()->getEntity();
        return $this->getRepositoryManager()
            ->addRepository($repository)
            ->getRepository($repository);
    }

    public function getRevisionForm($hydrate = true)
    {
        $form = $this->getOption('revision_form');
        
        if (! class_exists($form))
            throw new Exception\ClassNotFoundException(sprintf('Class %s not found!', $form));
        
        $form = new $form();
        if($hydrate && $this->hasCurrentRevision()){
            $data = array();
            foreach($this->getFields() as $field){
                $data[$field] = $this->getCurrentRevision()->get($field);
            }
            $form->setData($data);
        }
        
        return $form;
    }
    
    public function getFields(){
        return $this->getOption('fields');
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Versioning\Service\RepositoryServiceInterface::getCurrentRevision()
     */
    public function hasHead()
    {
        return $this->getRepository()->hasHead();
    }
    
    public function getHead()
    {
        return $this->getRepository()->getHead();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Versioning\Service\RepositoryServiceInterface::getCurrentRevision()
     */
    public function countRevisions()
    {
        return $this->getRepository()->countRevisions();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Versioning\Service\RepositoryServiceInterface::getCurrentRevision()
     */
    public function getCurrentRevision()
    {
        return $this->getRepository()->getCurrentRevision();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Versioning\Service\RepositoryServiceInterface::getCurrentRevision()
     */
    public function hasCurrentRevision()
    {
        return $this->getRepository()->hasCurrentRevision();
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Versioning\Service\RepositoryServiceInterface::getRevision()
     */
    public function getRevision($id)
    {
        return $this->getRepository()->getRevision($id);
    }

    public function getAllRevisions()
    {
        $criteria = Criteria::create()->orderBy(array(
            "id" => "desc"
        ));
        return $this->getRepository()
            ->getRevisions()
            ->matching($criteria);
    }

    public function getTrashedRevisions()
    {
        $criteria = Criteria::create()->orderBy(array(
            "id" => "desc"
        ));
        return $this->getRepository()
            ->getRevisions()
            ->matching($criteria);
    }

    /**
     * (non-PHPdoc)
     *
     * @see \Versioning\Service\RepositoryServiceInterface::checkoutRevision()
     */
    public function checkout($revisionId)
    {
        $this->getRepository()->checkoutRevision($revisionId);
        return $this;
    }

    public function commitRevision(Form $form, UserServiceInterface $user)
    {
        $repository = $this->getRepository();
        
        $revision = $this->getEntityService()
            ->getEntity()
            ->newRevision();
        
        $this->getUuidManager()->injectUuid($revision);
        
        $revision->setAuthor($user->getEntity());
        
        $repository->addRevision($revision);
        
        foreach ($form->getData() as $key => $value) {
            if (is_string($key) && is_string($value)){
                $revision->set($key, $value);
            }
        }
        
        $this->getObjectManager()->persist($revision);
        
        return $this;
    }

    public function removeRevision($revisionId)
    {
        $revision = $this->getRepository()->getRevision($revisionId);
        $this->getRepository()->removeRevision($revision);
        return $this;
    }

    public function trashRevision($revisionId)
    {
        $revision = $this->getRepository()->getRevision($revisionId);
        $revision->toggleTrashed();
        $this->getObjectManager()->persist($revision);
        return $this;
    }

    public function isUnrevised()
    {
        return $this->getRepository()->isUnrevised();
    }

}