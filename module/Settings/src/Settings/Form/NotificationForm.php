<?php
namespace Settings\Form;

use Zend\Form\Form;

class NotificationForm extends Form
{
    
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
//        $this->add(array(
//            'name' => 'email',
//            'type' => 'text',
//        ));
        
        $this->add(array(
            'name' => 'email_follow_me',
            'type' => 'checkbox',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
            'attributes' => array(
                'value' => '0',
            ),
        ));
        
        $this->add(array(
            'name' => 'email_comments_on_my_post',
            'type' => 'checkbox',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
            'attributes' => array(
                'value' => '0',
            ),
        ));
        
        $this->add(array(
            'name' => 'email_comments_on_a_post_i_care',
            'type' => 'checkbox',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
            'attributes' => array(
                'value' => '0',
            ),
        ));
        
        $this->add(array(
            'name' => 'email_like_my_sound',
            'type' => 'checkbox',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
            'attributes' => array(
                'value' => '0',
            ),
        ));
        
        $this->add(array(
            'name' => 'push_follow_me',
            'type' => 'checkbox',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
            'attributes' => array(
                'value' => '0',
            ),
        ));
        
        
        $this->add(array(
            'name' => 'push_comments_on_my_post',
            'type' => 'checkbox',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
            'attributes' => array(
                'value' => '0',
            ),
        ));
        
        $this->add(array(
            'name' => 'push_comments_on_a_post_i_care',
            'type' => 'checkbox',
            'options' => array(
                'checked_value' => '1',
                'unchecked_value' => '0',
            ),
            'attributes' => array(
                'value' => '0',
            ),
        ));
        
        $this->add(array(
            'name' => 'push_like_my_sound',
            'type' => 'checkbox',
            'options' => array(
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
