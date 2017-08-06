<?php
namespace User\Form;

use Zend\Form\Form;

class UploadCoverImageForm extends Form
{
    
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
        $this->add(array(
            'name' => 'cover_image',
            'type' => 'file',
            'options' => array(
                'label' => 'Cover Image:',
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
