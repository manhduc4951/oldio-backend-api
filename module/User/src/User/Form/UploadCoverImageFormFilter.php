<?php
namespace User\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UploadCoverImageFormFilter implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    protected $serviceLocator;
    
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function getPathUpload()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['user_cover_path_upload'];
    }
    
    public function getBucket()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['default_bucket'];
    }
    
    public function getImageType()
    {   
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['image_type'];
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
        
        
        /*cover image s3 filter*/
        $filterCoverImage = $this->getServiceLocator()->get('FilterManager')->get('S3RenameUpload');
        $filterCoverImage->setOptions(array(
            'bucket'    => $this->getBucket(),
            'target'    => $this->getPathUpload(),
            'randomize' => true,
            'use_upload_name' => true,
        ));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'cover_image',
            'required' => true,
            'filters'  => array(
                $filterCoverImage,
            ),            
            'validators' => array(
                array(
                    'name' => 'File\Extension',
                    'options' => array(
                        'extension' => $this->getImageType(),
                    ),
                ),
            ),            
        )));

        $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
}
