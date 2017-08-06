<?php
namespace SoundSet\Form;

use Zend\Form\Form;

class UserSoundSetFormSearch extends Form
{
    
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
		    'name' => 'name',
		    'attributes' => array(
		        'type' => 'text',
		    ),
		    'options' => array(
		        'label' => 'SoundSet name:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
		    )
		));
        
        $this->add(array(
		    'name' => 'user',
		    'attributes' => array(
		        'type' => 'text',
		    ),
		    'options' => array(
		        'label' => 'Buyer:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
		    )
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
            'name' => 'price_from',
            'type' => 'text',
            'options' => array(
                'label' => 'Price:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
            ),
            'attributes' => array(
                'class' => 'short-form',
            ),
        ));
        
        $this->add(array(
            'name' => 'price_to',
            'type' => 'text',
            'attributes' => array(
                'class' => 'short-form',
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
