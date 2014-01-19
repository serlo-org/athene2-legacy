<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author       Aeneas Rekkas (aeneas.rekkas@serlo.org]
 * @license      LGPL-3.0
 * @license      http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link         https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright    Copyright (c] 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/]
 */
namespace Authorization\Service;

use Authorization\Entity\PermissionInterface;

interface PermissionServiceInterface
{

    /**
     * @param int $id
     * @return PermissionInterface
     */
    public function getPermission($id);

    /**
     * @param string $name
     * @return PermissionInterface
     */
    public function findPermissionByName($name);

    /**
     * @return PermissionInterface[]
     */
    public function findAllPermissions();
} 