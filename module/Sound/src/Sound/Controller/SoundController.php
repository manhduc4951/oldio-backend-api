<?php
namespace Sound\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class SoundController extends AbstractActionController
{
    protected $soundDao;
    
    protected $categoryDao;
    
    protected $soundCategoryDao;
    
    protected $permissionDao;
    
    protected $soundBusiness;
    
    protected $userDao;
    
    public function getSoundDao()
    {
        if(!$this->soundDao) {
            $sm = $this->getServiceLocator();
            $this->soundDao = $sm->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    
    public function getCategoryDao()
    {
        if(!$this->categoryDao) {
            $sm = $this->getServiceLocator();
            $this->categoryDao = $sm->get('User\Model\CategoryDao');
        }
        return $this->categoryDao;
    }
    
    public function getPermissionDao()
    {
        if(!$this->permissionDao) {
            $sm = $this->getServiceLocator();
            $this->permissionDao = $sm->get('Permission\Model\PermissionDao');
        }
        return $this->permissionDao;
    }
    
    public function getSoundCategoryDao()
    {
        if(!$this->soundCategoryDao) {
            $sm = $this->getServiceLocator();
            $this->soundCategoryDao = $sm->get('User\Model\SoundCategoryDao');
        }
        return $this->soundCategoryDao;
    }
    
    public function getSoundBusiness()
    {
        if(!$this->soundBusiness) {
            $sm = $this->getServiceLocator();
            $this->soundBusiness = $sm->get('Sound\Business\SoundBussiness');
        }
        return $this->soundBusiness;
    }
    
    public function getUserDao()
    {
        if(!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    /**
     * Listing all sounds and show in backend
     * 
     * @return void
     */
    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('Config');
        /*Fetch data && paginate*/
        $request = $this->getRequest();
        $query = null;
        if($request->isGet()) {            
            $query = $this->params()->fromQuery();
            if(!empty($query['created_at'])) {
                $created = explode(' - ',$query['created_at']);
                $query['created_at_from'] = (!empty($created[0])) ? $created[0].' 00:00:00' : null;
                $query['created_at_to'] = (!empty($created[1])) ? $created[1].' 23:59:59' : null;
            }
            if(!empty($query['category_id'])) {
                $query['sound_ids'] = $this->getCategorySounds($query['category_id']);
            }
        }
        $data = $this->getSoundDao()->fetchAllSound(true,$query);
        $paginator = $data['paginator'];        
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));        
        $paginator->setItemCountPerPage($config['config_ica467']['item_per_page']);
        $page = (int)$this->params()->fromQuery('page', 1);
        
        /*Form search*/
        $formSearch = new \Sound\Form\SoundFormSearch;
        $formSearch->get('category_id')->setOptions(array('value_options' => $this->getCategories()));
        if($data['query']) {
            $formSearch->setData($data['query']);
        }
        
        /*Flash message*/
        $messages = '';
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
               $messages = $flashMessenger->getMessages();
        }
        
        return array(
            'sounds' => $paginator,
            'messages' => $messages,
            'formSearch' => $formSearch,
            'permissions' => $this->permissionActions(array('add','edit','delete','view'),'sound_sound'),
        );
    }
    
    /**
     * View details information of a sound(record) from backend
     * 
     * @return
     */
    public function viewAction()
    {
        $soundId = $this->params()->fromRoute('id',0);
        $sound = $this->getSoundDao()->fetchOneDetail((int)$soundId,'backend');
        //if($sound->thumbnail) {
        //    $sound->thumbnail = $this->url()->fromRoute('sound_thumbnail', array('file' => $sound->thumbnail),array('force_canonical' => true));        
        //}
        if($sound->tags) {
            $sound->tags = explode(' ',$sound->tags);
            foreach($sound->tags as &$tag) {
                $tag = '#'.$tag;    
            }
            $sound->tags = implode(', ',$sound->tags);
        }
        
        return array(
            'sound' => $sound,
        );
    }
    
    /**
     * Create a sound from backend
     * 
     * @return void
     */
    public function addAction()
    {
        $form = new \Sound\Form\SoundForm;
        $form->get('category_id')->setOptions(array('value_options' => $this->getCategories()));
        $form->get('user_id')->setOptions(array('value_options' => $this->getUsers()));
        
        $request = $this->getRequest();
        if($request->isPost()) {
            $formFilter = new \Sound\Form\SoundFormFilter;
            $formFilter->setServiceLocator($this->getServiceLocator());
            $form->setInputFilter($formFilter->getInputFilter());
            $data = array_merge($request->getPost()->toArray(),$_FILES);
            $form->setData($data);
            if($form->isValid()) {
                $dataForm = $form->getData();
                $sound = new \Sound\Model\Dto\SoundDto;
                $sound->exchangeArray($dataForm);
                $sound->category_id = $dataForm['category_id'];
                $this->getSoundBusiness()->createSoundRest($sound);
                $this->flashMessenger()->addMessage('Create sound successfully');
			    return $this->redirect()->toRoute('sound');
            }
        }
        
        return array(
            'form' => $form,
        );    
    }
    
    /**
     * Edit a sound from backend
     * 
     * @return void
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('sound', array(
                'action' => 'add'
            ));
        }
        $request = $this->getRequest();
        $sound = $this->getSoundDao()->fetchOne($id);
        if(!$sound) {
            return $this->redirect()->toRoute('sound');
        }
        $sound->category_id = $this->getSoundCategories($id);
        $oldThumbnail = $sound->thumbnail;
        $oldSoundPath = $sound->sound_path;
        $form = new \Sound\Form\SoundForm;
        $form->get('category_id')->setOptions(array('value_options' => $this->getCategories()));
        $form->get('user_id')->setOptions(array('value_options' => $this->getUsers()));
        $form->bind($sound);
        
        if($request->isPost()) {
            $formFilter = new \Sound\Form\SoundFormFilter;
            $formFilter->setServiceLocator($this->getServiceLocator());
            $data = array_merge($request->getPost()->toArray(),$_FILES);
            if(!$data['thumbnail']['tmp_name']) {
                $formFilter->getInputFilter()->get('thumbnail')->setAllowEmpty(true);
            }
            if(!$data['sound_path']['tmp_name']) {
                $formFilter->getInputFilter()->get('sound_path')->setAllowEmpty(true);
            }
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($data);
            if($form->isValid()) {
                $sound->old_thumbnail = $oldThumbnail;
                $sound->old_sound_path = $oldSoundPath;
                $sound->category_id = $data['category_id'];
                $this->getSoundBusiness()->updateSoundRest($sound);
                $this->flashMessenger()->addMessage('Update sound successfully');
			    return $this->redirect()->toRoute('sound');
            }
        }
        $config = $this->getServiceLocator()->get('config');
        $thumbnail = ($sound->thumbnail) ? $this->getS3Url($config['config_ica467']['sound_thumbnail_path_upload'].'/'.$sound->thumbnail) : null;
        return array(
            'form' => $form,
            'id' => $id,
            'files' => array(
                'thumbnail' => $thumbnail,
            ),
        );
    }
    
    /**
     * Delete a sound from backend
     * 
     * @return
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('sound',array('action' => 'index'));
        }
        $sound = $this->getSoundDao()->fetchOne($id);
        if(!$sound) {
            return $this->redirect()->toRoute('sound');    
        }
        $this->getSoundBusiness()->deleteSoundRest($sound);
        return $this->redirect()->toRoute('sound',array('action'=>'index'));
    }
    
    /**
     * Get list all categories and bind to form
     * 
     * @return array
     */
    public function getCategories()
    {  
        $categories = $this->getCategoryDao()->fetchAll();
        $categoriesArray = array();
        foreach($categories as $category) {
            $categoriesArray[$category->id] = $category->name;
        }
        return $categoriesArray;
        
    }
    
    /**
     * Get all categories id of a sound (using when edit a sound)
     * 
     * @return array
     */
    public function getSoundCategories($soundId)
    {
        $categoriesId = $this->getSoundCategoryDao()->fetchAllBy('sound_id',$soundId);
        $categoriesArray = array();
        foreach($categoriesId as $categoryId) {
             $categoriesArray[] = $categoryId->category_id;
        }
        
        return $categoriesArray;
    }
    
    /**
     * Get all sounds id belong to a category(using to search)
     * 
     * @param mixed $categoryId
     * @return void
     */
    public function getCategorySounds($categoryId)
    {
        $soundIds = array();
        foreach($this->getSoundCategoryDao()->fetchAllBy('category_id',$categoryId) as $sound) {
            $soundIds[] = $sound->sound_id;
        }
        
        return $soundIds;
    }
    
    /**
     * Get list all users and bind to form
     * 
     * @return void
     */
    public function getUsers()
    {
        $users = $this->getUserDao()->fetchAll();
        $user_array = array();
        foreach($users as $user) {
            $user_array[$user->id] = $user->username;
        }
        return $user_array;
    }
    
}