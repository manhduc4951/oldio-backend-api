<?php
namespace Settings\Form;

use Zend\Form\Form;

class AppConfigForm extends Form
{
    
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
        $this->add(array(
            'name' => 'name',
            'type' => 'text',
            'options' => array(
                'label' => 'Name:',
            ),
        ));
        
        $this->add(array(
            'name' => 'value',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Value:',
            ),
        ));
		
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Save',
                'class' => 'btn btn-primary btn-large',
			),
		));
	}
}
