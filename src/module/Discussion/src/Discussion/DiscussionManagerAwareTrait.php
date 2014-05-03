<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Discussion;

trait DiscussionManagerAwareTrait
{

    /**
     * @var DiscussionManagerInterface
     */
    protected $discussionManager;

    /**
     * @return DiscussionManagerInterface $discussionManager
     */
    public function getDiscussionManager()
    {
        return $this->discussionManager;
    }

    /**
     * @param DiscussionManagerInterface $discussionManager
     * @return self
     */
    public function setDiscussionManager(DiscussionManagerInterface $discussionManager)
    {
        $this->discussionManager = $discussionManager;

        return $this;
    }
}
