<?php
namespace Core\Entity;

use Core\Structure\AbstractAdapter;

abstract class AbstractEntityAdapter extends AbstractAdapter
{
    public function __construct(EntityInterface $entity){
        $this->setAdaptee($entity);
    }
    
	public function setEntity(EntityInterface $entity){
        $this->setAdaptee($entity);
        return $this;
    }
    
    public function getEntity(){
        return $this->getAdaptee();
    }
    
    public abstract function getFieldValues();
    
    public function setFieldValues(array $data){
        foreach($data as $key => $value)
            $this->setFieldValue($key, $value);
    }
    
    public function getFieldValue($field){
        return $this->getEntity()->get($field);
    }
    
    public function setFieldValue($field, $value){
        $this->getEntity()->set($field) = $value;
        return $this;
    }
    
    public function getId(){
        return $this->getEntity()->get('id');
    }
    
    abstract public function delete(){
        return $this;
    }
}