<?php
namespace Sound\Form;

use Zend\Form\Form;

class SoundFormSearch extends Form
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
		        'label' => 'Author:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
		    )
		));
        
        $this->add(array(
		    'name' => 'title',
		    'attributes' => array(
		        'type' => 'text',
		    ),
		    'options' => array(
		        'label' => 'Title:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
		    )
		));
        
        $this->add(array(
            'name' => 'type',
            'type' => 'multi_checkbox',
            'options' => array(
                'label' => 'Type:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
                'value_options' => array(
                    \Sound\Model\Dto\SoundDto::SOUND_TYPE_BROADCAST => 'Broadcast',
                    \Sound\Model\Dto\SoundDto::SOUND_TYPE_PENDING => 'Pending',
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'connect_facebook',
            'type' => 'multi_checkbox',
            'options' => array(
                'label' => 'Connect facebook:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
                'value_options' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'connect_twitter',
            'type' => 'multi_checkbox',
            'options' => array(
                'label' => 'Connect twitter:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
                'value_options' => array(
                    '1' => 'Yes',
                    '0' => 'No',
                ),
            ),
        ));
        
        $this->add(array(
            'name' => 'category_id',
            'type' => 'select',
            'options' => array(
                'label' => 'Category:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
                'empty_option' => '',
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
                'class' => 'btn btn-primary',
			),
		));
	}
}
