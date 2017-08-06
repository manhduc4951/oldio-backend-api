<?php
namespace User\Model\Dto;

class CategoryDto
{
    
    public function getProperties()
    {
        return array('id','name','description','icon','image','created_at');
    }
    
    public function exchangeArray($data)
    {        
        foreach($data as $key => $value) {
            if(in_array($key,$this->getProperties())) {
                $this->{$key} = $value;    
            }
        }        
    }
    
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }
    
    
}