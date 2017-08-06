<?php
namespace AdminUser\Form;

use Zend\Form\Form;

class AdminUserForm extends Form
{
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
            'name' => 'uid',
            'type' => 'hidden',
            
        ));
        
		$this->add(array(
		    'name' => 'username',
		    'attributes' => array(
		        'type' => 'text',
		    ),
		    'options' => array(
		        'label' => 'Username:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
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
            'name' => 'role',
            'type' => 'select',
            'options' => array(
                'label' => 'Role',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'status',
            'type' => 'select',
            'options' => array(
                'label' => 'Active/Inactive',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
                'value_options' => array(
                    '1' => 'Active',
                    '0' => 'Inactive'
                ),
            ),
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
