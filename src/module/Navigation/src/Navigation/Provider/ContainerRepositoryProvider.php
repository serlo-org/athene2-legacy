<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author    Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license   LGPL-3.0
 * @license   http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link      https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright Copyright (c) 2013-2014 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Navigation\Provider;

use Instance\Manager\InstanceManagerInterface;
use Navigation\Entity\PageInterface;
use Navigation\Entity\ParameterInterface;
use Navigation\Exception\ContainerNotFoundException;
use Navigation\Manager\NavigationManagerInterface;

class ContainerRepositoryProvider implements ContainerProviderInterface
{
    /**
     * @var NavigationManagerInterface
     */
    protected $navigationManager;
    /**
     * @var InstanceManagerInterface
     */
    protected $instanceManager;

    public function __construct(
        InstanceManagerInterface $instanceManager,
        NavigationManagerInterface $navigationManager
    ) {
        $this->navigationManager = $navigationManager;
        $this->instanceManager   = $instanceManager;
    }

    public function provide($container)
    {
        $instance = $this->instanceManager->getInstanceFromRequest();
        $pages    = [];

        try {
            $container = $this->navigationManager->findContainerByNameAndInstance($container, $instance);
        } catch (ContainerNotFoundException $e) {
            return [];
        }

        foreach ($container->getPages() as $page) {
            $addPage = $this->buildPage($page);

            $hasUri      = isset($addPage['uri']);
            $hasMvc      = isset($addPage['action']) || isset($addPage['controller']) || isset($addPage['route']);
            $hasProvider = isset($addPage['provider']);

            if ($hasUri || $hasMvc || $hasProvider) {
                $pages[] = $addPage;
            }
        }

        return $pages;
    }

    protected function buildPage(PageInterface $page)
    {
        $config = [];

        foreach ($page->getParameters() as $parameter) {
            $config = array_merge($config, $this->buildParameter($parameter));
        }

        foreach ($page->getChildren() as $child) {
            $addPage = $this->buildPage($child);

            $hasUri      = isset($addPage['uri']);
            $hasMvc      = isset($addPage['action']) || isset($addPage['controller']) || isset($addPage['route']);
            $hasProvider = isset($addPage['provider']);

            if ($hasUri || $hasMvc || $hasProvider) {
                $config['pages'][] = $addPage;
            }
        }

        return $config;
    }

    protected function buildParameter(ParameterInterface $parameter, &$config = [])
    {
        $key = $parameter->getKey() !== null ? (string)$parameter->getKey() : $parameter->getId();

        if ($parameter->hasChildren()) {
            foreach ($parameter->getChildren() as $child) {
                $this->buildParameter($child, $config[$key]);
            }
        } else {
            $config[$key] = $parameter->getValue();
        }

        return $config;
    }
}
