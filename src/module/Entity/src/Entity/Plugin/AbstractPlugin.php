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
namespace Entity\Plugin;

abstract class AbstractPlugin implements PluginInterface
{
    use \Entity\Service\EntityServiceAwareTrait;

    /**
     * @var string
     */
    protected $identity;
    
    /**
     * @var array
     */
    protected $options;
    
    /**
     * @return string $identity
     */
    public function getIdentity()
    {
        return $this->identity;
    }

	/**
     * @param string $identity
     * @return $this
     */
    public function setIdentity($identity)
    {
        $this->identity = $identity;
        return $this;
    }

	public function setOptions(array $options){
        $this->options = array_merge_recursive($this->getDefaultConfig(), $options);
        return $this;
    }
    
    public function getOption($name){
        return $this->options[$name];
    }
    
    abstract function getDefaultConfig();
}