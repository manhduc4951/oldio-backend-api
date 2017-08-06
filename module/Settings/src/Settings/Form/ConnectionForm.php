<?php
namespace Settings\Form;

use Zend\Form\Form;

class ConnectionForm extends Form
{
    
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
        $this->add(array(
            'name' => 'connect_facebook',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Connect to facebook',
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
            'attributes' => array(
                'value' => '0',
            ),
        ));
        
        $this->add(array(
            'name' => 'connect_twitter',
            'type' => 'checkbox',
            'options' => array(
                'label' => 'Connect to twitter',
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
            'attributes' => array(
                'value' => '0',
            ),
        ));
		
		$this->add(array(
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Save',
			),
		));
	}
}
