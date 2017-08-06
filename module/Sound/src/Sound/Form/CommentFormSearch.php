<?php
namespace Sound\Form;

use Zend\Form\Form;

class CommentFormSearch extends Form
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
		    'name' => 'sound',
		    'attributes' => array(
		        'type' => 'text',
		    ),
		    'options' => array(
		        'label' => 'Sound:',
                'label_attributes' => array(
                    'class' => 'checkbox inline',
                ),
		    )
		));
        
        $this->add(array(
		    'name' => 'comment',
		    'attributes' => array(
		        'type' => 'text',
		    ),
		    'options' => array(
		        'label' => 'Comment:',
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
			'name' => 'submit',
			'attributes' => array(
				'type' => 'submit',
				'value' => 'Search',
                'class' => 'btn btn-primary search-button',
			),
		));
	}
}
