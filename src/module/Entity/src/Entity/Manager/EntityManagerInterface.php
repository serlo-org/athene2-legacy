<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Entity\Manager;

use Common\ObjectManager\Flushable;
use Doctrine\Common\Collections\Collection;
use Entity\Entity\EntityInterface;
use Instance\Entity\InstanceInterface;

interface EntityManagerInterface extends Flushable
{

    /**
     * @param string            $type
     * @param array             $data
     * @param InstanceInterface $instance
     * @return EntityInterface
     */
    public function createEntity($type, array $data = [], InstanceInterface $instance);

    /**
     * @param string $name
     * @return EntityInterface[]|Collection
     */
    public function findEntitiesByTypeName($name);

    /**
     * @param int $id
     * @return EntityInterface
     */
    public function getEntity($id);
}
