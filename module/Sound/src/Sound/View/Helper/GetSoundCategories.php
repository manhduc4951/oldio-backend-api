<?php
namespace Sound\View\Helper;

use Zend\View\Helper\AbstractHelper;

class GetSoundCategories extends AbstractHelper
{
    protected $serviceLocator;
    
    protected $soundCategoryDao;
    
    protected $categoryDao;
    
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function getSoundCategoryDao()
    {
        if(!$this->soundCategoryDao) {
            $this->soundCategoryDao = $this->getServiceLocator()->get('User\Model\SoundCategoryDao');
        }
        return $this->soundCategoryDao;
    }
    
    public function getCategoryDao()
    {
        if(!$this->categoryDao) {
            $this->categoryDao = $this->getServiceLocator()->get('User\Model\CategoryDao');
        }
        return $this->categoryDao;
    }
    
    public function __invoke($soundId)
	{ 
       $categoriesId = $this->getSoundCategoryDao()->fetchAllBy('sound_id',$soundId);
       $categoriesArray = array();
       foreach($categoriesId as $categoryId) {
            $categoriesArray[] = $categoryId->category_id;
       }
       
       if(!empty($categoriesArray)) {
           $categories = $this->getCategoryDao()->fetchAllInArray($categoriesArray);
           $categoriesString = '';
           foreach($categories as $category) {
                $categoriesString .= $category->name.'</br>'; 
           }
           return $categoriesString; 
       }
       return '';
       
	}
}