<?php
namespace User\Form;

use Zend\Form\Form;

class UserForm extends Form
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
            'name' => 'facebook_id',
            'type' => 'text',
            'options' => array(
                'label' => 'Facebook Id',
            ),
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
            'name' => 'avatar',
            'type' => 'file',
            'options' => array(
                'label' => 'Avatar',
            ),
        )); 
		
		$this->add(array(
            'name' => 'cover_image',
            'type' => 'file',
            'options' => array(
                'label' => 'Cover image',
            ),
        ));
        
        $this->add(array(
            'name' => 'display_name',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Display name:',
            )
        ));
        
        $this->add(array(
            'name' => 'full_name',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Full name:',
            )
        ));
        
        $this->add(array(
            'name' => 'phone',
            'attributes' => array(
                'type' => 'text',
            ),
            'options' => array(
                'label' => 'Phone:',
            )
        ));
        
        $this->add(array(
            'name' => 'birthday',
            'attributes' => array(
                'type' => 'text',
                'class' => 'datepicker',
            ),
            'options' => array(
                'label' => 'Birthday:',
            )
        ));
        
        $this->add(array(
            'name' => 'gender',
            'type' => 'select',
            'options' => array(
                'label' => 'Gender:',
                'value_options' => array(
                    '1' => 'Male',
                    '2' => 'Female',
                ),
            )
        ));
        
        $this->add(array(
            'name' => 'country_id',
            'type' => 'select',
            'options' => array(
                'label' => 'Country:',
                'disable_inarray_validator' => true,
            ),
        ));
        
        $this->add(array(
            'name' => 'description',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Description',
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
