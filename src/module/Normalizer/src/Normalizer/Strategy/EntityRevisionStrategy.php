<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 */
namespace Normalizer\Strategy;

use Entity\Entity\Revision;

class EntityRevisionStrategy extends AbstractStrategy
{

    /**
     * @return Revision
     */
    public function getObject()
    {
        return $this->object;
    }

    public function isValid($object)
    {
        return $object instanceof Revision;
    }

    protected function getContent()
    {
        return $this->getObject()->get('content');
    }

    protected function getField($field, $fallback = null)
    {
        if ($this->getObject()->get($field) !== null) {
            return $this->getObject()->get($field);
        } elseif ($fallback !== null && $this->getObject()->get($fallback) !== null) {
            return $this->getObject()->get($fallback);
        } else {
            return $this->getObject()->getId();
        }
    }

    protected function getId()
    {
        return $this->getObject()->getId();
    }

    protected function getPreview()
    {
        $description = $this->getObject()->get('description');
        $description = $description ? : $this->getObject()->get('content');
        return $description;
    }

    protected function getRouteName()
    {
        return 'entity/repository/compare';
    }

    protected function getRouteParams()
    {
        return [
            'entity'   => $this->getObject()->getRepository()->getId(),
            'revision' => $this->getObject()->getId()
        ];
    }

    protected function getTimestamp()
    {
        return $this->getObject()->getTimestamp();
    }

    protected function getTitle()
    {
        return $this->getObject()->get('title') ? : $this->object->getId();
    }

    protected function getType()
    {
        return $this->getObject()->getRepository()->getType()->getName();
    }
}
