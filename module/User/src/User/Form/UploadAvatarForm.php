<?php
namespace User\Form;

use Zend\Form\Form;

class UploadAvatarForm extends Form
{
    
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
        $this->add(array(
            'name' => 'avatar',
            'type' => 'file',
            'options' => array(
                'label' => 'Avatar',
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
