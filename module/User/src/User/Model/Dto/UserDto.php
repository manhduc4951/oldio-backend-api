<?php
namespace User\Model\Dto;

class UserDto
{
    const GENDER_MALE = 1;
    
    const GENDER_FEMALE = 2;
    
    public function getProperties()
    {
        return array('id','facebook_id','username','password','avatar','cover_image','display_name','full_name','phone','birthday','gender','country_id','storage_plan_id','storage_plan_updated_at','description','created_at','updated_at');
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