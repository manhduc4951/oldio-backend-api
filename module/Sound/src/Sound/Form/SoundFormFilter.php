<?php
namespace Sound\Form;

use Zend\InputFilter\Factory as InputFactory;     
use Zend\InputFilter\InputFilter;                 
use Zend\InputFilter\InputFilterAwareInterface;   
use Zend\InputFilter\InputFilterInterface;        

class SoundFormFilter implements InputFilterAwareInterface
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
    
    public function getPathUploadThumbnail()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['sound_thumbnail_path_upload'];
    }
    
    public function getPathUploadThumbnail2()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['sound_thumbnail2_path_upload'];
    }
    
    public function getPathUploadThumbnail3()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['sound_thumbnail3_path_upload'];
    }
    
    public function getPathUploadSound()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['sound_file_path_upload'];
    }
    
    public function getImageType()
    {   
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['image_type'];
    }
    
    public function getSoundType()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['sound_type'];
    }
    
    public function getBucket()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['default_bucket'];
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
                'name'     => 'user_id',
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
                            'min' => 1,
                            'max' => 9999999999,
                        ),
                    ),
                ),
            )));
            
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'category_id',
                'required' => true,
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'title',
                'required' => true,
                'filters'  => array(
                    //array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 255,
                        ),
                    ),
                  ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'description',
                'required' => false,
                'filters'  => array(
                    //array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 2000,
                        ),
                    ),
                  ),
            )));
            
            
          /*sound thumbnail s3 filter*/
        $filterThumbnail = $this->getServiceLocator()->get('FilterManager')->get('S3RenameUpload');
        $filterThumbnail->setOptions(array(
            'bucket'    => $this->getBucket(),
            'target'    => $this->getPathUploadThumbnail(),
            'randomize' => true,
            'use_upload_name' => true,
        ));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'thumbnail',
            'required' => false,
            'filters'  => array(                
                $filterThumbnail,
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
            
        $filterThumbnail2 = $this->getServiceLocator()->get('FilterManager')->get('S3RenameUpload');
        $filterThumbnail2->setOptions(array(
            'bucket'    => $this->getBucket(),
            'target'    => $this->getPathUploadThumbnail2(),
            'randomize' => true,
            'use_upload_name' => true,
        ));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'thumbnail2',
            'required' => false,
            'filters'  => array(                
                $filterThumbnail2,
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
        
        $filterThumbnail3 = $this->getServiceLocator()->get('FilterManager')->get('S3RenameUpload');
        $filterThumbnail3->setOptions(array(
            'bucket'    => $this->getBucket(),
            'target'    => $this->getPathUploadThumbnail3(),
            'randomize' => true,
            'use_upload_name' => true,
        ));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'thumbnail3',
            'required' => false,
            'filters'  => array(                
                $filterThumbnail3,
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
            
        /*sound path s3 filter*/
        $filterPath = $this->getServiceLocator()->get('FilterManager')->get('S3RenameUpload');
        $filterPath->setOptions(array(
            'bucket'    => $this->getBucket(),
            'target'    => $this->getPathUploadSound(),
            'randomize' => true,
            'use_upload_name' => true,
        ));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'sound_path',
            'required' => true,
            'filters'  => array(                
                $filterPath,
            ),            
            'validators' => array(
                array(
                    'name' => 'File\Extension',
                    'options' => array(
                        'extension' => $this->getSoundType(),
                    ),
                ),
            ),            
        )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'duration',
                'required' => false,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                    array('name' => 'Int'),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'connect_facebook',
                'required' => false,
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
                'name'     => 'connect_twitter',
                'required' => false,
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
                'name'     => 'tags',
                'required' => false,
                'filters'  => array(
                    //array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            //'max'      => 2000,
                        ),
                    ),
                  ),
            )));
            

            $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
}