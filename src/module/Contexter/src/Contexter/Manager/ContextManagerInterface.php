<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Contexter\Manager;

use Common\ObjectManager\Flushable;
use Contexter\Entity\ContextInterface;
use Contexter\Entity\RouteInterface;

interface ContextManagerInterface extends Flushable
{

    /**
     * @param int $id
     * @return ContextInterface
     */
    public function getContext($id);

    /**
     * @param int $id
     * @return RouteInterface
     */
    public function getRoute($id);

    /**
     * @param int    $objectId
     * @param string $type
     * @param string $title
     * @return ContextInterface
     */
    public function add($objectId, $type, $title);

    /**
     * @return ContextInterface[]
     */
    public function findAll();

    /**
     * @return array
     */
    public function findAllTypeNames();

    /**
     * @param int $id
     * @return void
     */
    public function removeRoute($id);

    /**
     * @param int $id
     * @return void
     */
    public function removeContext($id);
}
