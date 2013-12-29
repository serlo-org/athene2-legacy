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
namespace Token\Provider;

abstract class AbstractProvider implements ProviderInterface
{

    protected $object;

    /**
     *
     * @return mixed $reference
     */
    public function getObject()
    {
        return $this->object;
    }

    /**
     *
     * @param mixed $reference            
     * @return self
     */
    public function setObject($object)
    {
        $this->validObject($object);
        $this->object = $object;
        return $this;
    }
    
    abstract protected function validObject($object);
}