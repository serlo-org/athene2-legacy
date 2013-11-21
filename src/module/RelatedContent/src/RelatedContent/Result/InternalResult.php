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
namespace RelatedContent\Result;

use RelatedContent\Entity\InternalInterface;
use RelatedContent\Exception;
use RelatedContent\Entity\TypeInterface;

class InternalResult extends AbstractResult
{
    use\Common\Traits\RouterAwareTrait;

    /**
     *
     * @return InternalInterface
     */
    public function getObject()
    {
        return parent::getObject();
    }
    
    public function setObject(TypeInterface $object){
        if(!$object instanceof InternalInterface)
            throw new Exception\InvalidArgumentException(sprintf('Expected InternalInterface but got `%s`', get_class($object)));
        return parent::setObject($object);
    }

    public function getType()
    {
        return 'internal';
    }
    
    /*
     * (non-PHPdoc) @see \Related\Result\ResultInterface::getTitle()
     */
    public function getTitle()
    {
        return $this->getObject()->getTitle();
    }
    
    /*
     * (non-PHPdoc) @see \Related\Result\ResultInterface::getUrl()
     */
    public function getUrl()
    {
        return $this->getRouter()->assemble(array(
            'uuid' => $this->getObject()->getReference()
                ->getId()
        ), array(
            'name' => 'uuid/router'
        ));
    }
}