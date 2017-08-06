<?php

namespace Sound\Form;

use Zend\Form\Form;

class LikeForm extends Form {    
    

    public function __construct() {
        parent::__construct();
        
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
        $this->add(array(
            'name' => 'sound_id',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Sound id:',
            )
        ));
        
        $this->add(array(
            'name' => 'user_id',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'User id:',
            )
        )); 

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Like',
            ),
        ));
    }
    
}
