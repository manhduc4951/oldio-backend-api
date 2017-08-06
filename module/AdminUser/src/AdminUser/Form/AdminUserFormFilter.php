<?php
namespace AdminUser\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class AdminUserFormFilter implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    protected $dbAdapter;
    
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }
    
    public function setInputFilter (InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();
            $factory     = new InputFactory();
            
        $inputFilter->add($factory->createInput(array(
                'name'     => 'uid',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
        )));    
            
        $inputFilter->add($factory->createInput(array(
            'name' => 'username',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'Db\NoRecordExists',
                    'options' => array(
                        'table' => 'admin_user',
                        'field' => 'username',
                        'adapter' => $this->dbAdapter,
                        
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name' => 'password',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'StringLength',
                    'options' => array(
                        'min' => 6
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name' => 'confirm_password',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
           'validators' => array(
               array(
                   'name' => 'Identical',
                   'options' => array(
                       'token' => 'password',
                   ),
               ),
            )
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name' => 'role',
            'require' => 'true',
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                array('name' => 'Int'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Between',
                    'options' => array(
                        'min' => 1,
                        'max' => 10,
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name' => 'status',
            'require' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
                array('name' => 'Int'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Between',
                    'options' => array(
                        'min' => 0,
                        'max' => 1,
                    ),
                ),
            ),
        )));
            

        $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
}
