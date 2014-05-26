<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Notification\Filter;

use Doctrine\Common\Persistence\ObjectManager;
use Event\Filter\PersistentEventLogFilterChain;
use Zend\Filter\FilterChain;

class PersistentNotificationFilterChain extends FilterChain
{
    /**
     * @param ObjectManager $objectManager
     */
    public function __construct(ObjectManager $objectManager)
    {
        $this->attach(new PersistentEmptyFilter($objectManager));
        $this->attach(new PersistentEventLogFilterChain($objectManager));
    }
}
