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
namespace Normalizer\Strategy;

use User\Entity\UserInterface;

class UserStrategy extends AbstractStrategy
{

    /**
     * @return UserInterface
     */
    public function getObject()
    {
        return $this->object;
    }

    protected function getTitle()
    {
        return $this->getObject()->getUsername();
    }

    protected function getTimestamp()
    {
        return $this->getObject()->getDate();
    }

    protected function getContent()
    {
        return $this->getObject()->getUsername();
    }

    protected function getPreview()
    {
        return $this->getObject()->getUsername();
    }

    protected function getType()
    {
        return 'user';
    }

    protected function getRouteName()
    {
        return 'user/profile';
    }

    protected function getRouteParams()
    {
        return ['id' => $this->getObject()->getId()];
    }

    public function isValid($object)
    {
        return $object instanceof UserInterface;
    }
}