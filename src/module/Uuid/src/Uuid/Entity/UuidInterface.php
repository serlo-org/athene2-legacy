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
namespace Uuid\Entity;

interface UuidInterface
{

    /**
     *
     * @return int $id
     */
    public function getId ();

    /**
     *
     * @return string $uuid
     */
    public function getUuid ();

    /**
     *
     * @param string $uuid            
     * @return $this
     */
    public function setUuid ($uuid);
    
    /**
     * Sets an entities $id to this Uuid
     * 
     * @param UuidHolder $entity
     * @return $this
     */
    public function hydrate(UuidHolder $entity);
    
    public function getTrashed();
    
    public function setTrashed($trashed);
    
    public function is($type);
}