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

use Entity\Entity\EntityInterface;

class EntityStrategy extends AbstractStrategy
{

    /**
     * @return EntityInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    public function isValid($object)
    {
        return $object instanceof EntityInterface;
    }

    protected function getContent()
    {
        return $this->getField('content');
    }

    protected function getField($field, $fallback = null)
    {
        if ($this->getObject()->hasCurrentRevision()) {
            $revision = $this->getObject()->getCurrentRevision();
        } elseif (is_object($this->getObject()->getHead())) {
            $revision = $this->getObject()->getHead();
        } else {
            return $this->getObject()->getId();
        }

        if ($revision->get($field) !== null) {
            return $revision->get($field);
        } elseif ($fallback !== null && $revision->get($fallback) !== null) {
            return $revision->get($fallback);
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
        return $this->getField('summary', 'description', 'content');
    }

    protected function getRouteName()
    {
        return 'entity/page';
    }

    protected function getRouteParams()
    {
        return [
            'entity' => $this->getObject()->getId()
        ];
    }

    protected function getTimestamp()
    {
        return $this->getObject()->getTimestamp();
    }

    protected function getTitle()
    {
        return $this->getField('title', 'id');
    }

    protected function getType()
    {
        return $this->getObject()->getType()->getName();
    }
}
