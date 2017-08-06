<?php
namespace AdminUser\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class ChangePasswordFormFilter implements InputFilterAwareInterface
{
    protected $inputFilter;
    
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
            

        $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
}
