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
namespace Navigation\View\Helper;

use Zend\Cache\Storage\StorageInterface;

class Navigation extends \Zend\View\Helper\Navigation
{
    protected $storage;

    public function __construct(StorageInterface $storage)
    {
        $this->storage = $storage;
    }

    public function render($container = null)
    {
        $container = $container ? $container : $this->getContainer();
        $key       = hash('sha256', serialize($container));

        if ($this->storage->hasItem($key)) {
            return $this->storage->getItem($key);
        }

        $output = parent::render($container);
        $this->storage->setItem($key, $output);

        return $output;
    }
}
