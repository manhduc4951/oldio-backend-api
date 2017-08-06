<?php
namespace User\Form;

use Zend\Form\Form;

class RegisterForm extends Form
{
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
		$this->add(array(
		    'name' => 'username',
		    'attributes' => array(
		        'type' => 'text',
		    ),
		    'options' => array(
		        'label' => 'Username:',
		    )
		));
		
		$this->add(array(
			'name' => 'password',
			'attributes' => array(
				'type' => 'password',
			    'autocomplete' => 'off',
			),
			'options' => array(
				'label' => 'Password:',
			)
		));
		
		$this->add(array(
			'name' => 'confirm_password',
			'attributes' => array(
				'type' => 'password',
			    'autocomplete' => 'off',
			),
			'options' => array(
				'label' => 'Confirm Password:',
			)
		));
		
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Register',
			),
		));
	}
}
