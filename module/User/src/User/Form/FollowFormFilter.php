<?php
namespace User\Form;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class FollowFormFilter implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    public function setInputFilter(InputFilterInterface $inputFilter)
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
                'name'     => 'user_id_audience',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'Int'),
                ),                
                'validators' => array(
                    array(
                        'name'    => 'Between',
                        'options' => array(                            
                            'min'      => 1,
                            'max'      => 99999999999,
                        ),
                    ),
                ),                                
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'user_id_following',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'Int'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'Between',
                        'options' => array(                            
                            'min'      => 1,
                            'max'      => 99999999999,
                        ),
                    ),
                ),                  
            )));

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}