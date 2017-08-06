<?php
namespace Settings\Form;

use Zend\Form\Form;

class SoundQualityForm extends Form
{
    
	public function __construct()
	{
		parent::__construct();
		
        $this->add(array(
            'name' => 'id',
            'type' => 'hidden',
            
        ));
        
        $this->add(array(
            'name' => 'sound_quality',
            'type' => 'select',
            'options' => array(
                'label' => 'Type',
                'value_options' => array(
                    \Settings\Model\Dto\SettingsDto::SOUND_LOW_QUALITY => 'Low quality',
                    \Settings\Model\Dto\SettingsDto::SOUND_MEDIUM_QUALITY => 'Medium quality',
                    \Settings\Model\Dto\SettingsDto::SOUND_HIGH_QUALITY => 'High quality',
                ),
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
