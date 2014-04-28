<?php
/**
 * Athene2 - Advanced Learning Resources Manager
 *
 * @author      Aeneas Rekkas (aeneas.rekkas@serlo.org)
 * @license     LGPL-3.0
 * @license     http://opensource.org/licenses/LGPL-3.0 The GNU Lesser General Public License, version 3.0
 * @link        https://github.com/serlo-org/athene2 for the canonical source repository
 * @copyright   Copyright (c) 2013 Gesellschaft für freie Bildung e.V. (http://www.open-education.eu/)
 */
namespace Entity\View\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Entity\Entity\EntityInterface;
use Entity\Exception;
use Entity\Options\ModuleOptionsAwareTrait;
use Zend\View\Helper\AbstractHelper;

class EntityHelper extends AbstractHelper
{
    use ModuleOptionsAwareTrait;

    public function __invoke()
    {
        return $this;
    }

    public function findTaxonomyTerm(EntityInterface $entity, $type)
    {
        /* @var $term \Taxonomy\Entity\TaxonomyTermInterface */
        foreach ($entity->getTaxonomyTerms() as $term) {
            $ancestor = $term->findAncestorByTypeName($type);
            if ($ancestor) {
                return $ancestor;
            }
        }

        return null;
    }

    public function getVisible(Collection $entities)
    {
        return $entities->filter(
            function (EntityInterface $e) {
                return !$e->isTrashed() && $e->hasCurrentRevision();
            }
        );
    }

    public function asTypeCollection(Collection $entities)
    {
        $types = [];
        foreach ($entities as $e) {
            $types[$e->getType()->getName()][] = $e;
        }

        return new ArrayCollection($types);
    }

    public function getOptions(EntityInterface $entity)
    {
        return $this->getModuleOptions()->getType(
            $entity->getType()->getName()
        );
    }
}