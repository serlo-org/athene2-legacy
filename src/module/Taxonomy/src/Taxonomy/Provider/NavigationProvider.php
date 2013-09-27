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
namespace Taxonomy\Provider;

use Zend\ServiceManager\ServiceLocatorInterface;
use Doctrine\ORM\EntityManager;
use Application\Navigation\ProviderInterface;
use Taxonomy\Manager\TaxonomyManager;

class NavigationProvider implements ProviderInterface
{

    /**
     *
     * @var ServiceLocatorInterface
     */
    protected $serviceLocator;

    /**
     *
     * @var EntityManager
     */
    protected $entityManager;

    /**
     *
     * @var TaxonomyManager
     */
    protected $termManager;

    /**
     *
     * @var array
     */
    protected $defaultOptions = array(
        'name' => 'default',
        'route' => 'default',
        'params' => array()
    );

    protected $options;

    public function __construct(array $options, ServiceLocatorInterface $serviceLocator)
    {
        $this->options = array_merge($this->defaultOptions, $options);
        $this->serviceLocator = $serviceLocator;
        $this->entityManager = $serviceLocator->get('EntityManager');
        $this->languageService = $serviceLocator->get('Language\Manager\LanguageManager')->get($this->options['language']);
        $this->termManager = $serviceLocator->get('Taxonomy\Manager\SharedTaxonomyManager')->findTaxonomyByName($this->options['type'], $this->languageService)->findTermByAncestors((array) $this->options['parent']);
    }

    public function provideArray($maxDepth = 1)
    {
        /*$criteria = Criteria::create()->where(Criteria::expr()->isNull("parent"))
            ->setFirstResult(0)
            ->setMaxResults(20);*/
        if ($this->entityManager->isOpen())
            $this->entityManager->refresh($this->termManager->getEntity());
        
        //$terms = $this->termManager->getTerms()->matching($criteria);
        $terms = $this->termManager->getChildren();
        $return = $this->iterTerms($terms, $maxDepth);
        return $return;
    }

    protected function iterTerms($terms, $depth)
    {
        if ($depth == 0)
            return array();
        
        $return = array();
        foreach ($terms as $term) {
            $current = array();
            $current['route'] = $this->options['route'];
            $current['params'] = array_merge_recursive($this->options['params'], array(
                'path' => $term->getSlug()
            ));
            $current['label'] = $term->getName();
            $children = $term->getChildren();
            if (count($children)) {
                $current['pages'] = $this->iterTerms($children, $depth - 1);
            }
            $return[] = $current;
        }
        return $return;
    }
}