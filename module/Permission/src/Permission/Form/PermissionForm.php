<?php
namespace Permission\Form;
use Zend\Form\Form;

class PermissionForm extends Form
{
    public function __construct()
    {
        parent::__construct();
        
        $this->add(array(
            'name' => 'update',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Update',
                'class' => 'btn',
            ),
        ));
        
        $this->add(array(
            'name' => 'scan',
            'attributes' => array(
                'type' => 'submit',
                'value' => 'Reset',
                'class' => 'btn reset-acl',
            ),
        ));
    }
}
