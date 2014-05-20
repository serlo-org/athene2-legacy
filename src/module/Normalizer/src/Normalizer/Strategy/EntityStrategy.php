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

    protected function getField($field)
    {
        $entity = $this->getObject();
        $id     = $entity->getId();

        if (is_array($field)) {
            $fields = $field;
            $value  = '';
            foreach ($fields as $field) {
                $value = $this->getField((string)$field);
                if ($value && $value != $id) {
                    break;
                }
            }

            return $value ? : $id;
        }


        $revision = $entity->hasCurrentRevision() ? $entity->getCurrentRevision() : $entity->getHead();

        if (!$revision) {
            return $id;
        }

        $value = $revision->get($field);

        return $value ? : $id;
    }

    protected function getId()
    {
        return $this->getObject()->getId();
    }

    protected function getPreview()
    {
        return $this->getField(['summary', 'description', 'content']);
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
        return $this->getField(['title', 'id']);
    }

    protected function getType()
    {
        return $this->getObject()->getType()->getName();
    }
}
