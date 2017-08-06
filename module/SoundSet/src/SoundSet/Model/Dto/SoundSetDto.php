<?php
namespace SoundSet\Model\Dto;

class SoundSetDto
{
    
    public function getProperties()
    {
        return array('id','name','description','image','zip_file','price','creation','code','created_at','updated_at');
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