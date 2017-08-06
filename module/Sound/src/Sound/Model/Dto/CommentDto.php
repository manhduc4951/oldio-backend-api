<?php
namespace Sound\Model\Dto;

class CommentDto
{
    
    public function getProperties()
    {
        return array('id','sound_id','user_id','comment','created_at','updated_at');
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