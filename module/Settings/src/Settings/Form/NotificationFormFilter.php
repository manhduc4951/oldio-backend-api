<?php
namespace Settings\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class NotificationFormFilter implements InputFilterAwareInterface
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
                'name'     => 'id',
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
        )));    
            
//        $inputFilter->add($factory->createInput(array(
//            'name' => 'email',
//            'required' => true,
//            'filters' => array(
//                array('name' => 'StripTags'),
//                array('name' => 'StringTrim')
//            ),
//            'validators' => array(
//                array(
//                    'name' => 'EmailAddress',
//                ),
//            ),
//        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'email_follow_me',
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
                        'min' => 0,
                        'max' => 1,
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'email_comments_on_my_post',
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
                        'min' => 0,
                        'max' => 1,
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'email_comments_on_a_post_i_care',
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
                        'min' => 0,
                        'max' => 1,
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'email_like_my_sound',
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
                        'min' => 0,
                        'max' => 1,
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'push_follow_me',
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
                        'min' => 0,
                        'max' => 1,
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'push_comments_on_my_post',
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
                        'min' => 0,
                        'max' => 1,
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'push_comments_on_a_post_i_care',
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
                        'min' => 0,
                        'max' => 1,
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'push_like_my_sound',
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
