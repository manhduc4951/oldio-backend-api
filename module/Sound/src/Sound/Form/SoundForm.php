<?php
namespace Sound\Form;

use Zend\Form\Form;

class SoundForm extends Form
{
    
	public function __construct()
	{
		parent::__construct();
        
        $this ->setAttribute( 'enctype' , 'multipart/form-data' );
		
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
        $this->add(array(
            'name' => 'user_id',
            'type' => 'select',
            'options' => array(
                'label' => 'User id',
            ),
        ));
        
        $this->add(array(
            'name' => 'category_id',
            'type' => 'select',
            'options' => array(
                'label' => 'Category',
                'disable_inarray_validator' => true,
            ),
            'attributes' => array(
                'multiple' => 'multiple',
                'class' => 'select-multi',
            ),
        ));
        
        $this->add(array(
            'name' => 'title',
            'type' => 'text',
            'options' => array(
                'label' => 'Title',
            ),
        ));
        
        $this->add(array(
            'name' => 'thumbnail',
            'type' => 'file',
            'options' => array(
                'label' => 'Thumbnail'
            ),
        ));
        
        $this->add(array(
            'name' => 'thumbnail2',
            'type' => 'file',
            'options' => array(
                'label' => 'Thumbnail2'
            ),
        ));
        
        $this->add(array(
            'name' => 'thumbnail3',
            'type' => 'file',
            'options' => array(
                'label' => 'Thumbnail3'
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
            'name' => 'sound_path',
            'type' => 'file',
            'options' => array(
                'label' => 'Sound',
            ),
        ));
        
        $this->add(array(
            'name' => 'duration',
            'type' => 'text',
            'options' => array(
                'label' => 'Duration',
            ),
        ));
        
        $this->add(array(
            'name' => 'type',
            'type' => 'select',
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                    '1' => 'Broadcast',
                    '2' => 'Pending',
                ),
            ),
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
            'name' => 'tags',
            'type' => 'textarea',
            'options' => array(
                'label' => 'Tags',
            ),
            'attributes' => array(
                'placeholder' => "Each tag separate with a space "."eg:tag1 tag2 tag3",
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
