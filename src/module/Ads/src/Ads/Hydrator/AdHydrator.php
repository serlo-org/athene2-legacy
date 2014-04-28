<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Jakob Pfab (jakob.pfab@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright   Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Ads\Hydrator;

use Ads\Entity\AdInterface;
use Ads\Exception;
use Zend\Stdlib\ArrayUtils;
use Zend\Stdlib\Hydrator\HydratorInterface;

class AdHydrator implements HydratorInterface
{

    protected $keys = [
        'author',
        'title',
        'content',
        'attachment',
        'instance',
        'frequency',
        'url'
    ];

    public function extract($object)
    {
        $data = [];
        foreach ($this->keys as $key) {
            $method = 'get' . ucfirst($key);
            $data['key'] = $object->$method();
        }

        return $data;
    }

    public function hydrate(array $data, $object)
    {
        $data = ArrayUtils::merge($this->extract($object), $data);

        if (!$object instanceof AdInterface) {
            throw new Exception\InvalidArgumentException(sprintf(
                'Expected object to be AdInterface but got "%s"',
                get_class($object)
            ));
        }

        foreach ($this->keys as $key) {
            $method = 'set' . ucfirst($key);
            $value  = $this->getKey($data, $key);
            if ($value !== null) {
                $object->$method($value);
            }
        }

        return $object;
    }

    protected function getKey(array $data, $key)
    {
        return array_key_exists($key, $data) ? $data[$key] : null;
    }
}