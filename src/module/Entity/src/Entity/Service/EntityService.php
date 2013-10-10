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
namespace Entity\Service;

use Entity\Exception\InvalidArgumentException;
use Taxonomy\Collection\TermCollection;
use Zend\Stdlib\ArrayUtils;

class EntityService implements EntityServiceInterface
{
    use \Zend\ServiceManager\ServiceLocatorAwareTrait,\Entity\Plugin\PluginManagerAwareTrait,\Entity\Manager\EntityManagerAwareTrait,\Common\Traits\EntityDelegatorTrait,\Zend\EventManager\EventManagerAwareTrait, \Taxonomy\Manager\SharedTaxonomyManagerAwareTrait;

    protected $whitelistedPlugins = array();

    protected $pluginOptions = array();

    public function getTerms()
    {
        return new TermCollection($this->getEntity()->get('terms'), $this->getSharedTaxonomyManager());
    }

    public function getId()
    {
        return $this->getEntity()->getId();
    }

    public function setConfig(array $config)
    {
        $this->whitelistPlugins($config['plugins']);
        return $this;
    }

    public function hasPlugin($name)
    {
        return $this->isPluginWhitelisted($name);
    }

    public function isPluginWhitelisted($name)
    {
        return array_key_exists($name, $this->whitelistedPlugins) && $this->whitelistedPlugins[$name] !== FALSE;
    }

    public function getScopesForPlugin($plugin)
    {
        $return = array();
        foreach ($this->pluginOptions as $scope => $options) {
            if ($options['plugin'] == $plugin) {
                $return[] = $scope;
            }
        }
        return $return;
    }

    public function whitelistPlugins(array $config)
    {
        foreach ($config as $name => $data) {
            $this->whitelistPlugin($name, $data['plugin']);
            $this->setPluginOptions($name, $data);
        }
    }

    public function setPluginOptions($name, array $options)
    {
        if (isset($this->pluginOptions[$name])) {
            $options = ArrayUtils::merge($this->pluginOptions[$name], $options);
        }
        
        $this->pluginOptions[$name] = $options;
        return $this;
    }

    public function getPluginOptions($name)
    {
        return (array_key_exists($name, $this->pluginOptions) && array_key_exists('options', $this->pluginOptions[$name])) ? $this->pluginOptions[$name]['options'] : array();
    }

    public function whitelistPlugin($name, $plugin)
    {
        $this->whitelistedPlugins[$name] = $plugin;
        return $this;
    }

    public function getPlugin($name)
    {
        return $this->whitelistedPlugins[$name];
    }

    /**
     * Get plugin instance
     *
     * @param string $name
     *            Name of plugin to return
     * @return mixed
     */
    public function plugin($name)
    {
        if (! $this->isPluginWhitelisted($name)) {
            throw new InvalidArgumentException(sprintf('Plugin %s is not whitelisted for this entity.', $name));
        }
        
        $pluginManager = $this->getPluginManager();
        
        $pluginManager->setEntityService($this);
        $pluginManager->setPluginOptions($this->getPluginOptions($name));
        
        return $this->getPluginManager()->get($this->getPlugin($name));
    }

    /**
     * Method overloading: return/call plugins
     * If the plugin is a functor, call it, passing the parameters provided.
     * Otherwise, return the plugin instance.
     *
     * @param string $method            
     * @param array $params            
     * @return mixed
     */
    public function __call($method, $params)
    {
        if ($this->isPluginWhitelisted($method)) {
            $plugin = $this->plugin($method);
            if (is_callable($plugin)) {
                return call_user_func_array($plugin, $params);
            }
            return $plugin;
        } else {
            if (method_exists($this->getEntity(), $method)) {
                return call_user_func_array(array(
                    $this->getEntity(),
                    $method
                ), $params);
            } else {
                throw new \Exception(sprintf('Method %s not defined.', $method));
            }
        }
    }
    
    public function isVoided(){
        return $this->getEntity()->getVoided();
    }
}