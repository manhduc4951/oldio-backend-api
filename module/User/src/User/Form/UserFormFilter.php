<?php
namespace User\Form;

use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class UserFormFilter implements InputFilterAwareInterface
{
    protected $inputFilter;
    
    protected $dbAdapter;
    
    protected $serviceLocator;
    
    public function setServiceLocator($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function getUserAvatarPathUpload()
    {
        $config = $this->getServiceLocator()->get('config');
        return $config['config_ica467']['user_avatar_path_upload'];
    }
    
    public function getUserCoverImagePathUpload()
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
            'name' => 'facebook_id',
            'required' => false,
            'filters' => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim')
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
        
        /*avatar s3 filter*/
        $filterAvatar = $this->getServiceLocator()->get('FilterManager')->get('S3RenameUpload');
        $filterAvatar->setOptions(array(
            'bucket'    => $this->getBucket(),
            'target'    => $this->getUserAvatarPathUpload(),
            'randomize' => true,
            'use_upload_name' => true,
        ));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'avatar',
            'required' => false,
            'filters'  => array(                
                $filterAvatar,
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

        /*cover image s3 filter*/
        $filterCoverImage = $this->getServiceLocator()->get('FilterManager')->get('S3RenameUpload');
        $filterCoverImage->setOptions(array(
            'bucket'    => $this->getBucket(),
            'target'    => $this->getUserCoverImagePathUpload(),
            'randomize' => true,
            'use_upload_name' => true,
        ));
        $inputFilter->add($factory->createInput(array(
            'name'     => 'cover_image',
            'required' => false,
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
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'display_name',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
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
                array(
                    'name' => 'Db\NoRecordExists',
                    'options' => array(
                        'table' => 'user',
                        'field' => 'display_name',
                        'adapter' => $this->dbAdapter,
                        
                    ),
                ),
              ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'full_name',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
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
            'name'     => 'phone',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
//            'validators' => array(
//                array(
//                    'name'    => 'StringLength',
//                    'options' => array(
//                        'encoding' => 'UTF-8',
//                        'min'      => 6,
//                        'max'      => 30,
//                    ),
//                ),
//              ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'birthday',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
            'validators' => array(
                array(
                    'name'    => 'Date',
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'gender',
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
                        'max' => 2,
                    ),
                ),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'country_id',
            'required' => false,
            'filters'  => array(
                array('name' => 'StripTags'),
                array('name' => 'StringTrim'),
            ),
        )));
        
        $inputFilter->add($factory->createInput(array(
            'name'     => 'description',
            'required' => true,
            'filters'  => array(
                array('name' => 'StripTags'),
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

        $this->inputFilter = $inputFilter;
        }

        return $this->inputFilter;
    }
    
}
