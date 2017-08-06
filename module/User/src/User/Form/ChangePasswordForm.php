<?php
namespace User\Form;

use Zend\Form\Form;

class ChangePasswordForm extends Form
{
    
	public function __construct()
	{
		parent::__construct();
		
		$this->add(array(
			'name' => 'current_password',
            'type' => 'password',
			'attributes' => array(
			    'autocomplete' => 'off',
			),
		));
        
        $this->add(array(
            'name' => 'new_password',
            'type' => 'password',
            'attributes' => array(
			    'autocomplete' => 'off',
			),
        ));
        
        $this->add(array(
            'name' => 'confirm_password',
            'type' => 'password',
            'attributes' => array(
			    'autocomplete' => 'off',
			),
        ));
		
		$this->add(array(
			'name' => 'submit',
            'type' => 'submit',
		));
	}
}
