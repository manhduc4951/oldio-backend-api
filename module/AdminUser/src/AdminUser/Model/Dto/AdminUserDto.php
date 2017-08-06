<?php
namespace AdminUser\Model\Dto;

class AdminUserDto
{
    
    public function getProperties()
    {
        return array('uid','username','password','status','created_at','updated_at');
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