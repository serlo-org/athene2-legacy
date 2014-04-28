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

interface EntityManagerAwareInterface
{

    /**
     * @return EntityManagerInterface $entityManager
     */
    public function getEntityManager();

    /**
     * @param EntityManagerInterface $entityManager
     * @return self
     */
    public function setEntityManager(\Entity\Manager\EntityManagerInterface $entityManager);
}