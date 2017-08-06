<?php
namespace User\Form;

use Zend\Form\Form;

class UserFormSearch extends Form
{
	public function __construct()
	{
		parent::__construct();
        
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
		    'name' => 'display_name',
		    'attributes' => array(
		        'type' => 'text',
		    ),
		    'options' => array(
		        'label' => 'Display Name:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
		    )
		));
        
        $this->add(array(
		    'name' => 'full_name',
		    'attributes' => array(
		        'type' => 'text',
		    ),
		    'options' => array(
		        'label' => 'Full Name:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
		    )
		));
        
        $this->add(array(
            'name' => 'gender',
            'type' => 'multi_checkbox',
            'options' => array(
                'label' => 'Gender:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
                'value_options' => array(
                    \User\Model\Dto\UserDto::GENDER_MALE => 'Male',
                    \User\Model\Dto\UserDto::GENDER_FEMALE => 'Female',
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'country_id',
            'type' => 'select',
            'options' => array(
                'label' => 'Country:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
                'empty_option' => '',
            ),
            'attributes' => array(
                'class' => 'select-country',
            ),
        ));
        
        $this->add(array(
            'name' => 'birthday',
            'type' => 'text',
            'attributes' => array(
                'class' => 'daterangepicker',
            ),
            'options' => array(
                'label' => 'Birthday:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'created_at',
            'type' => 'text',
            'attributes' => array(
                'class' => 'daterangepicker',
            ),
            'options' => array(
                'label' => 'Created date:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
            ),
        ));
		
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Search',
                'class' => 'btn btn-primary search-button',
			),
		));
	}
}
