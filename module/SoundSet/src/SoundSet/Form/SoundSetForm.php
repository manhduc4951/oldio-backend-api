<?php
namespace SoundSet\Form;

use Zend\Form\Form;

class SoundSetForm extends Form
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
		    )
		));
        
        $this->add(array(
            'name' => 'description',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Description:'
            ),
        ));
        
        $this->add(array(
            'name' => 'image',
            'type' => 'file',
            'options' => array(
                'label' => 'Image:'
            ),
        ));
        
        $this->add(array(
            'name' => 'zip_file',
            'type' => 'file',
            'options' => array(
                'label' => 'Zip file:'
            ),
        ));
        
        $this->add(array(
            'name' => 'price',
            'type' => 'text',
            'options' => array(
                'label' => 'Price:',
            ),
            'attributes' => array(
                'placeholder' => '$0 to $99.99',
            ),
        ));
        
        $this->add(array(
            'name' => 'creation',
            'type' => 'text',
            'options' => array(
                'label' => 'Creation:',
            ),
        ));
        
        $this->add(array(
            'name' => 'code',
            'type' => 'text',
            'options' => array(
                'label' => 'Code:'
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
