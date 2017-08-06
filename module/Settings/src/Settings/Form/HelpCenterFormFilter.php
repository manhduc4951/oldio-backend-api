<?php
namespace Settings\Form;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class HelpCenterFormFilter implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    protected $dbAdapter;
    
    public function setDbAdapter($dbAdapter)
    {
        $this->dbAdapter = $dbAdapter;
    }
    
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
                'name'     => 'name',
                'required' => true,
                'validators' => array(
                    array(
                        'name' => 'Db\NoRecordExists',
                        'options' => array(
                            'table' => 'help_center',
                            'field' => 'name',
                            'adapter' => $this->dbAdapter,
                            
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'value',
                'required' => true,
            )));
            

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}