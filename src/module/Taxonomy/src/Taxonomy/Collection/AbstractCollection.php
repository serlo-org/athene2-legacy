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
namespace Taxonomy\Collection;

use Doctrine\ORM\PersistentCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Selectable;

abstract class AbstractCollection implements Collection, Selectable
{
    /**
     *
     * @var Manager
     */
    protected $manager;

    protected $container;
    
    public function getComponent($component){
        return $component->getEntity();
    }

    /**
     *
     * @return \Core\Manager $manager
     */
    public function getManager ()
    {
        return $this->manager;
    }

    /**
     *
     * @param \Core\Manager $manager            
     * @return $this
     */
    public function setManager($manager)
    {
        $this->manager = $manager;
        return $this;
    }

    /**
     *
     * @return new Collections $collection
     */
    public function getCollection ()
    {
        return $this->collection;
    }

    /**
     *
     * @param new Collections\ArrayCollection( $collection            
     * @return $this
     */
    public function setCollection (Collection $collection)
    {
        $this->collection = $collection;
        return $this;
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::add()
     */
    public function add ($element)
    {
        return $this->getCollection()->add($this->getComponent($element));
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::clear()
     */
    public function clear ()
    {
        return $this->getCollection()->clear();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::contains()
     */
    public function contains ($element)
    {
        return $this->getCollection()->clear($this->getComponent($element));
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::isEmpty()
     */
    public function isEmpty ()
    {
        return $this->getCollection()->isEmpty();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::remove()
     */
    public function remove ($key)
    {
        return $this->getCollection()->remove($key);
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::removeElement()
     */
    public function removeElement ($element)
    {
        return $this->getCollection()->removeElement($this->getComponent($element));
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::containsKey()
     */
    public function containsKey ($key)
    {
        return $this->getCollection()->containsKey($key);
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::getKeys()
     */
    public function getKeys ()
    {
        return $this->getCollection()->getKeys();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::getValues()
     */
    public function getValues ()
    {
        return $this->getCollection()->getValues();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::set()
     */
    public function set ($key, $value)
    {
        return $this->getCollection()->set($key, $value);
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::toArray()
     */
    public function toArray ()
    {
        return $this->getCollection()->toArray();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::first()
     */
    public function first ()
    {
        return $this->getCollection()->first();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::last()
     */
    public function last ()
    {
        return $this->getCollection()->last();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::key()
     */
    public function key ()
    {
        return $this->getCollection()->key();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::current()
     */
    public function current ()
    {
        return $this->getCollection()->current();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::next()
     */
    public function next ()
    {
        return $this->getCollection()->next();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::exists()
     */
    public function exists (\Closure $p)
    {
        return $this->getCollection()->exists($p);
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::filter()
     */
    public function filter (\Closure $p)
    {
        return $this->getCollection()->exists($p);
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::forAll()
     */
    public function forAll (\Closure $p)
    {
        return $this->getCollection()->forAll($p);
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::map()
     */
    public function map (\Closure $func)
    {
        return $this->getCollection()->map($func);
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::partition()
     */
    public function partition (\Closure $p)
    {
        return $this->getCollection()->partition($p);
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::indexOf()
     */
    public function indexOf ($element)
    {
        return $this->getCollection()->indexOf($this->getComponent($element));
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::slice()
     */
    public function slice ($offset, $length = null)
    {
        return $this->getCollection()->slice($offset, $length);
    }
    
    /*
     * (non-PHPdoc) @see ArrayAccess::offsetExists()
     */
    public function offsetExists ($offset)
    {
        return $this->getCollection()->offsetExists($offset);
    }
    
    /*
     * (non-PHPdoc) @see ArrayAccess::offsetGet()
     */
    public function offsetGet ($offset)
    {
        return $this->getCollection()->offsetGet($offset);
    }
    
    /*
     * (non-PHPdoc) @see ArrayAccess::offsetSet()
     */
    public function offsetSet ($offset, $value)
    {
        return $this->getCollection()->offsetSet($offset, $value);
    }
    
    /*
     * (non-PHPdoc) @see ArrayAccess::offsetUnset()
     */
    public function offsetUnset ($offset)
    {
        return $this->getCollection()->offsetUnset($offset);
    }
    
    protected $services;
    
    /*
     * (non-PHPdoc) @see IteratorAggregate::getIterator()
     */
    public function getIterator ()
    {
        if($this->asService){
            $array = array();
            foreach($this->getCollection() as $key => $element){
                $array[$key] = $this->getManager()->get($element);
            }
            return new \ArrayIterator($array);      
        } else {
            return $this->getCollection()->getIterator();
        }
    }
    
    /*
     * (non-PHPdoc) @see Countable::count()
     */
    public function count ()
    {
        return $this->getCollection()->count();
    }
    
    /*
     * (non-PHPdoc) @see \Doctrine\Common\Collections\Collection::get()
     */
    public function get ($key)
    {
        return $this->getCollection()->get($key);
    }

    public function getFromManager ($key)
    {
        return $this->getManager()->get($this->get($key));
    }
    
    protected $asService = false;
    
    public function asService(){
        $this->asService = true;
        return $this;
    }
    
    public function asEntity(){
        $this->asService = true;
        return $this;
    }

    /**
     *
     * @param \Doctrine\Common\Collections\Criteria $criteria            
     */
    public function matching (\Doctrine\Common\Collections\Criteria $criteria)
    {
        if($this->getCollection() instanceof PersistentCollection)
            throw new \Exception('Collection is not a PersistentCollection.');
        
        return $this->getCollection()->matching($criteria);
    }
}