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
namespace Link\Manager;

trait LinkManagerAwareTrait
{

    /**
     *
     * @var LinkManagerInterface
     */
    protected $linkManager;

    /**
     *
     * @return LinkManagerInterface
     *         $linkManager
     */
    public function getLinkManager ()
    {
        return $this->linkManager;
    }

    /**
     *
     * @param LinkManagerInterface $linkManager            
     * @return $this
     */
    public function setLinkManager (LinkManagerInterface $linkManager)
    {
        $this->linkManager = $linkManager;
        return $this;
    }
}