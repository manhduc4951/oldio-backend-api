<?php
namespace SoundSet\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class UserSoundSetController extends AbstractActionController
{
    protected $userSoundSetDao;
    
    public function getUserSoundSetDao()
    {
        if(!$this->userSoundSetDao) {
            $sm = $this->getServiceLocator();
            $this->userSoundSetDao = $sm->get('SoundSet\Model\UserSoundSetDao');
        }
        return $this->userSoundSetDao;
    }
    
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
        }
        $data = $this->getUserSoundSetDao()->fetchAllUserSoundSet(true,$query);
        $paginator = $data['paginator'];        
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));        
        $paginator->setItemCountPerPage($config['config_ica467']['item_per_page']);
        $page = (int)$this->params()->fromQuery('page', 1);
        
        /*Form search*/
        $formSearch = new \SoundSet\Form\UserSoundSetFormSearch;
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
            'userSoundSets' => $paginator,
            'messages' => $messages,
            'formSearch' => $formSearch,
            //'permissions' => $this->permissionActions(array('add','edit','delete','view'),'soundset_soundset'),
        );
    }
}