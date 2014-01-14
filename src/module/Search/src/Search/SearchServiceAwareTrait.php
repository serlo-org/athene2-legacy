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
namespace Search;

trait SearchServiceAwareTrait
{

    /**
     *
     * @var SearchServiceInterface
     */
    protected $searchService;

    /**
     *
     * @return SearchServiceInterface $searchService
     */
    public function getSearchService()
    {
        return $this->searchService;
    }

    /**
     *
     * @param SearchServiceInterface $searchService            
     * @return self
     */
    public function setSearchService(SearchServiceInterface $searchService)
    {
        $this->searchService = $searchService;
        return $this;
    }
}
