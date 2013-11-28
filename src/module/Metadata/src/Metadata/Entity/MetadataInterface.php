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
namespace Metadata\Entity;

use Uuid\Entity\UuidInterface;

interface MetadataInterface
{

    /**
     *
     * @return int
     */
    public function getId();

    /**
     *
     * @return string
     */
    public function getValue();

    /**
     *
     * @return MetadataKeyInterface
     */
    public function getKey();

    /**
     *
     * @return UuidInterface
     */
    public function getObject();

    /**
     *
     * @param UuidInterface $object            
     * @return $this
     */
    public function setObject(UuidInterface $object);

    /**
     *
     * @param MetadataKeyInterface $key            
     * @return $this
     */
    public function setKey(MetadataKeyInterface $key);

    /**
     *
     * @param unknown $value            
     * @return $this
     */
    public function setValue($value);
}