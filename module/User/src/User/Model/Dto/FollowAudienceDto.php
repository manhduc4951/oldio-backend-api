<?php
namespace User\Model\Dto;

class FollowAudienceDto
{
    
    public function getProperties()
    {
        return array('id','user_id_audience','user_id_following');
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