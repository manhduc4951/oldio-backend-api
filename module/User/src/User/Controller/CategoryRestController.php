<?php
namespace User\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class CategoryRestController extends AbstractMyRestfulController
{  
    protected $categoryDao;
    
    protected $soundCategoryDao;
    
    protected $soundDao;
    
    public function getCategoryDao()
    {
        if (!$this->categoryDao) {
            $sm = $this->getServiceLocator();
            $this->categoryDao = $sm->get('User\Model\CategoryDao');
        }
        return $this->categoryDao;
    }
    
    public function getSoundCategoryDao()
    {
        if(!$this->soundCategoryDao) {
            $sm = $this->getServiceLocator();
            $this->soundCategoryDao = $sm->get('User\Model\SoundCategoryDao');
        }
        return $this->soundCategoryDao;
    }
    
    public function getSoundDao()
    {
        if(!$this->soundDao) {
            $sm = $this->getServiceLocator();
            $this->soundDao = $sm->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    /**
     * Get list all categories
     * 
     * @return
     */
    public function getList()
    {
        $categories = $this->getCategoryDao()->fetchAll();
        $filePaths = array(
            'image' => 'category_image',
            'icon' => 'category_icon',
        );
        $categories = $this->getObjectsUrl($categories,$filePaths);
        
        return $this->success($categories);    
    }

    /**
     * Get all users have sounds which belong to a category
     * 
     * @param mixed $id
     * @return
     */
    public function get($id)
    {  
        $category_id = (int) $id;
        $userId = $this->identity()->id;
        $category = $this->getCategoryDao()->fetchOne($category_id);
        if(!$category) {
            return $this->error('The category you request does not exist',400);    
        }
        
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $updated_at = $this->params()->fromQuery('updated_at',null);
        
        $criteria = array(
            'category_id' => $category_id,
            'limit' => $limit,
            'offset' => $offset,
            'updated_at' => $updated_at,
        );
        $users = $this->getSoundCategoryDao()->fetchUsersBelongCategory($criteria);
        $filePaths = array(
            'avatar' => 'user_avatar_path_upload',
        );
        $users = $this->getObjectsUrl($users,$filePaths);
        /*User own sound or in list following: count private sounds*/
        foreach($users as &$user) {
            $userIdFollowings = $this->getFollowingUsers($user['user_id']);
            if($userId == $user['user_id'] || in_array($userId,$userIdFollowings)) {
                $user['sounds'] = $user['sounds'];    
            } else {
                $user['sounds'] = (string)($user['sounds']- count($this->getSoundDao()->fetchPrivateSounds($user['user_id'])));    
            }
        }
        
        return $this->success($users);
    }

    public function create($data) {}

    public function update($id, $data) {}

    public function delete($id) {}

}
