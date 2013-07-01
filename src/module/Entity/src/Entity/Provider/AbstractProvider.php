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
namespace Entity\Provider;

abstract class AbstractProvider implements Provider {
    protected $publicMethods = array();
    
    protected $identity;

    /**
	 * @return string $identity
	 */
	public function getIdentity() {
		return $this->identity;
	}

	/**
	 * @param string $identity
	 * @return $this
	 */
	public function setIdentity($identity) {
		$this->identity = $identity;
		return $this;
	}

	public function isMethodPublic($method){
        return in_array($method, $this->publicMethods);
    }
    
    public function identify ()
    {
        if (! $this->identity)
            $this->identity = uniqid();
        
        return $this->identity;
    }

    public function providesMethod ($method)
    {
        return $this->isMethodPublic($method) && method_exists($this, $method);
    }
}