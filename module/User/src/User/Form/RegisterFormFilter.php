<?php
namespace User\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class RegisterFormFilter implements InputFilterAwareInterface
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
                'name'     => 'id',
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
                    'name' => 'EmailAddress',
                ),
                array(
                    'name' => 'Db\NoRecordExists',
                    'options' => array(
                        'table' => 'user',
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
                        'min' => 8
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
            

        $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
}
