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
namespace Core\Decorator;

//use Core\Component\ComponentInterface;

class AbstractDecorator implements DecoratorInterface
{
	/**
	 * @var mixed
	 */
    protected $concreteComponent;

    /**
     * @return the $concreteComponent
     */
    public function getConcreteComponent ()
    {
        return $this->concreteComponent;
    }

    /**
     * @param field_type $concreteComponent            
     */
    public function setConcreteComponent ($concreteComponent)
    {
        $this->concreteComponent = $concreteComponent;
    }

    public function __call ($method, $args)
    {
        return call_user_func_array(array(
            $this->concreteComponent,
            $method
        ), $args);
    }

    public function __construct ($concreteComponent = NULL)
    {
        $this->concreteComponent = $concreteComponent;
    }

    public function providesMethod ($method)
    {
        if (method_exists($this, $method)) {
            return true;
        } elseif ($this->concreteComponent instanceof DecoratorInterface) {
            return $this->concreteComponent->providesMethod($method);
        } else {
            return method_exists($this->concreteComponent, $method);
        }
        return false;
    }
    
    public function isInstanceOf($class){
        return (($this instanceof $class) || ($this->concreteComponent instanceof $class));
    }
}