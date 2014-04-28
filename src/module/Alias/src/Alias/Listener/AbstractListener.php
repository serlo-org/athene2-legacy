<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Alias\Listener;

use Alias\AliasManagerAwareTrait;
use Alias\AliasManagerInterface;
use Common\Listener\AbstractSharedListenerAggregate;

abstract class AbstractListener extends AbstractSharedListenerAggregate
{
    use AliasManagerAwareTrait;

    /**
     * @param AliasManagerInterface $aliasManager
     */
    public function __construct(AliasManagerInterface $aliasManager)
    {
        $this->aliasManager = $aliasManager;
    }
}