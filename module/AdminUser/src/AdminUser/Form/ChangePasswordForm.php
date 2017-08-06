<?php
namespace AdminUser\Form;

use Zend\Form\Form;

class ChangePasswordForm extends Form
{
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
            'name' => 'uid',
            'type' => 'hidden',
            
        ));
		
		$this->add(array(
			'name' => 'password',
			'attributes' => array(
				'type' => 'password',
			    'autocomplete' => 'off',
			),
			'options' => array(
				'label' => 'Password:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
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
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
			)
		));
		
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Save',
                'class' => 'btn',
			),
		));
	}
}
