<?php
namespace Settings\Model\Dto;

class StoragePlanDto
{
    
    public function getProperties()
    {
        return array('id','name','description','image');
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