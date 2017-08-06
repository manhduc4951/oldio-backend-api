<?php
namespace AdminUser\Form;

use Zend\Form\Form;

class AdminUserFormSearch extends Form
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
            'name' => 'rid',
            'type' => 'multi_checkbox',
            'options' => array(
                'label' => 'Role Permission:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'status',
            'type' => 'multi_checkbox',
            'options' => array(
                'label' => 'Active/Inactive:',
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
