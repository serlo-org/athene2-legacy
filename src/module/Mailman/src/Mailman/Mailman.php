<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Mailman;

use Zend\ServiceManager\ServiceLocatorInterface;

class Mailman implements MailmanInterface
{
    /**
     * @var Options\ModuleOptions
     */
    protected $moduleOptions;

    /**
     * @var \Zend\ServiceManager\ServiceLocatorInterface
     */
    protected $serviceLocator;

    protected $adapters = [];

    /**
     * @param Options\ModuleOptions   $moduleOptions
     * @param ServiceLocatorInterface $serviceLocator
     */
    public function __construct(Options\ModuleOptions $moduleOptions, ServiceLocatorInterface $serviceLocator){
        $this->moduleOptions = $moduleOptions;
        $this->serviceLocator = $serviceLocator;
    }

    /**
     * @return string $defaultSender
     */
    public function getDefaultSender()
    {
        return $this->moduleOptions->getSender();
    }

    public function send($to, $from, $subject, $body)
    {
        $this->loadAdapters();
        foreach ($this->adapters as $adapter) {
            /* @var $adapter Adapter\AdapterInterface */
            $adapter->addMail($to, $from, $subject, $body);
        }
        $this->flush();
    }

    public function flush()
    {
        $this->loadAdapters();
        foreach ($this->adapters as $adapter) {
            /* @var $adapter Adapter\AdapterInterface */
            $adapter->flush();
        }
    }

    protected function loadAdapters()
    {
        foreach ($this->moduleOptions->getAdapters() as $adapter) {
            if (!array_key_exists($adapter, $this->adapters)) {
                $this->adapters[$adapter] = $this->serviceLocator->get($adapter);
                if (!$this->adapters[$adapter] instanceof Adapter\AdapterInterface) {
                    throw new Exception\RuntimeException(sprintf(
                        '%s does not implement AdapterInterface',
                        get_class($this->adapters[$adapter])
                    ));
                }
            }
        }
    }
}