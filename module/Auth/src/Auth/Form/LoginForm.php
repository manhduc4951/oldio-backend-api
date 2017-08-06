<?php

namespace Auth\Form;

use Zend\Form\Form;

class LoginForm extends Form {

    public function __construct() {
        parent::__construct();

        $this->add(array(
            'name' => 'username',
            'attributes' => array(
                'type' => 'text',
                'class' => 'input-block-level',
                'placeholder' => 'Username',
            ),
        ));

        $this->add(array(
            'name' => 'password',
            'attributes' => array(
                'type' => 'password',
                'class' => 'input-block-level',
                'placeholder' => 'Password',
            ),
        ));

        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Login',
                'class' => 'btn btn-large btn-primary',
            ),
        ));
    }
    
    public function getByName()
    {
        $formNames = array_keys($this->byName);
        $formSubmit = array_search('submit',$formNames);
        unset($formNames[$formSubmit]);
        return $formNames;
        
    }

}
