<?php

namespace Sound\Form;

use Zend\Form\Form;

class CommentForm extends Form {    
    

    public function __construct() {
        parent::__construct();
        
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
        $this->add(array(
            'name' => 'sound_id',
            'type' => 'select',
            'options' => array(
                'label' => 'Sound:',
                'disable_inarray_validator' => true
            )
        ));
        
        $this->add(array(
            'name' => 'user_id',
            'type' => 'select',
            'options' => array(
                'label' => 'Author:',
                'disable_inarray_validator' => true
            )
        ));
        
        $this->add(array(
            'name' => 'comment',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Comment:',
            )
        )); 

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Save',
                'class' => 'btn btn-large btn-primary',
            ),
        ));
    }
    
}
