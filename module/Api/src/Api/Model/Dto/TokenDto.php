<?php
namespace Api\Model\Dto;

class TokenDto
{
    
    public function getProperties()
    {
        return array('access_token','client_id','user_id','expires','scope');
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