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
namespace Entity\Plugin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Entity\Exception;
use Zend\Mvc\Router\RouteMatch;
use Zend\Stdlib\RequestInterface;
use Zend\Stdlib\ResponseInterface;

abstract class AbstractController extends AbstractActionController
{
    use \Entity\Manager\EntityManagerAwareTrait;

    protected $entityService;

    // This is a very ugly hack and breaks other modules.
    public function dispatchD(RequestInterface $request, ResponseInterface $response = NULL)
    {
        if ($this->getEntityService()->hasPlugin('learningResource')) {
            $subject = $this->getEntityService()
                ->learningResource()->getDefaultSubject()->getSlug();
            
            if ($subject !== NULL) {
                $routeMatch = new RouteMatch(array(
                    'subject' => $subject
                ));
                $routeMatch->setMatchedRouteName('subject');
                $this->getServiceLocator()
                    ->get('Ui\Navigation\DefaultNavigationFactory')
                    ->setRouteMatch($routeMatch);
            }
        }
        
        return parent::dispatch($request, $response);
    }

    public function getEntityService($id = NULL)
    {
        if (! $this->entityService) {
            
            if (! $id) {
                $id = $this->params('entity');
            }
            
            $this->entityService = $this->getEntityManager()->getEntity($id);
        }
        
        return $this->entityService;
    }

    /**
     *
     * @param string $id            
     * @throws \Exception
     */
    protected function getPlugin($id = NULL)
    {
        $entity = $this->getEntityService($id);
        
        if (! $entity->isPluginWhitelisted($this->params('plugin')))
            throw new Exception\RuntimeException(sprintf('Plugin %s not supported.', $this->params('plugin')));
        
        $scope = $this->params('plugin');
        
        return $entity->$scope();
    }
}