<?php

namespace User\Form;

use Zend\Form\Form;

class FollowForm extends Form {    
    

    public function __construct() {
        parent::__construct();
        
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
        $this->add(array(
            'name' => 'user_id_audience',
            'attributes' => array(
                'type' => 'text',
            ),
        ));
        
        $this->add(array(
            'name' => 'user_id_following',
            'attributes' => array(
                'type' => 'text',
            ),
        )); 

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Follow',
            ),
        ));
    }
    
}
