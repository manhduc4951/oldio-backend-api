<?php
namespace SoundSet\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class SoundSetFormFilter implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    protected $serviceLocator;
    
    public function setInputFilter (InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getSoundSetImagePathUpload()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['sound_set_image_upload_local'];
    }
    
    public function getSoundSetZipPathUpload()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['sound_set_zip_upload_local'];
    }
    
    public function getImageType()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['image_type'];
    }
    
    public function getBucket()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['default_bucket'];
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
            'name' => 'name',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
            'validators' => array(
                array(
                    'name' => 'Db\NoRecordExists',
                    'options' => array(
                        'table' => 'sound_set',
                        'field' => 'name',
                        'adapter' => $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'),
                        
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'description',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'image',
            'required' => false,
            'filters'  => array(                
                array('name' => 'file_renameupload',
                      'options' => array(
                            'target' => $this->getSoundSetImagePathUpload(),                           
                            'randomize' => true,
                            'use_upload_name' => true,
                       ),
                ),
               
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
        
        /*soundset image s3 filter - USING LOCAL UPLOAD*/
//        $filterImage = $this->getServiceLocator()->get('FilterManager')->get('S3RenameUpload');
//        $filterImage->setOptions(array(
//            'bucket'    => $this->getBucket(),
//            'target'    => $this->getSoundSetImagePathUpload(),
//            'randomize' => true,
//            'use_upload_name' => true,
//        ));
//        $inputFilter->add($factory->createInput(array(
//            'name'     => 'image',
//            'required' => false,
//            'filters'  => array(                
//                $filterImage,
//            ),            
//            'validators' => array(
//                array(
//                    'name' => 'File\Extension',
//                    'options' => array(
//                        'extension' => $this->getImageType(),
//                    ),
//                ),
//            ),            
//        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'zip_file',
            'required' => true,
            'filters'  => array(                
                array('name' => 'file_renameupload',
                      'options' => array(
                            'target' => $this->getSoundSetZipPathUpload(),                           
                            'randomize' => true,
                            'use_upload_name' => true,
                       ),
                ),
               
            ),            
            'validators' => array(
                array(
                    'name' => 'File\Extension',
                    'options' => array(
                        'extension' => array('zip'),
                    ),
                ),
            ),            
        )));
        
        /*zip file s3 filter - USING LOCAL UPLOAD*/
//        $filterZip = $this->getServiceLocator()->get('FilterManager')->get('S3RenameUpload');
//        $filterZip->setOptions(array(
//            'bucket'    => $this->getBucket(),
//            'target'    => $this->getSoundSetZipPathUpload(),
//            'randomize' => true,
//            'use_upload_name' => true,
//        ));
//        $inputFilter->add($factory->createInput(array(
//            'name'     => 'zip_file',
//            'required' => true,
//            'filters'  => array(                
//                $filterZip,
//            ),            
//            'validators' => array(
//                array(
//                    'name' => 'File\Extension',
//                    'options' => array(
//                        'extension' => array('zip'),
//                    ),
//                ),
//            ),            
//        )));
        
        $inputFilter->add($factory->createInput(array(
            'name' => 'price',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
//            'validators' => array(
//                array(
//                    'name' => 'Float',
//                ),
//                array(
//                    'name' => 'Between',
//                    'min' => 0,
//                    'max' => 99,
//                ),
//            ),
        ))); 
        
        $inputFilter->add($factory->createInput(array(
            'name' => 'creation',
            'required' => true,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name' => 'code',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
            ),
        ))); 

        $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
}
