<?php
namespace Sound\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class CommentController extends AbstractActionController
{
    protected $commentDao;
    
    protected $userDao;
    
    protected $soundDao;
    
    public function getCommentDao()
    {
        if(!$this->commentDao) {
            $sm = $this->getServiceLocator();
            $this->commentDao = $sm->get('Sound\Model\CommentDao');
        }
        return $this->commentDao;
    }
    
    public function getUserDao()
    {
        if(!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
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
     * Listing all comments and show in backend
     * 
     * @return
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
        }
        $data = $this->getCommentDao()->fetchAllComment(true,$query);
        $paginator = $data['paginator'];        
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));        
        $paginator->setItemCountPerPage($config['config_ica467']['item_per_page']);
        $page = (int)$this->params()->fromQuery('page', 1);
        
        /*Form search*/
        $formSearch = new \Sound\Form\CommentFormSearch;
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
            'comments' => $paginator,
            'messages' => $messages,
            'formSearch' => $formSearch,
            'permissions' => $this->permissionActions(array('add','edit','delete'),'sound_comment'),
        );
    }
    
    /**
     * Create a comment from backend
     * 
     * @return
     */
    public function addAction()
    {
        $form = new \Sound\Form\CommentForm;
        $form->get('user_id')->setOptions(array('value_options' => $this->getUsers()));
        $form->get('sound_id')->setOptions(array('value_options' => $this->getSounds()));
        
        $request = $this->getRequest();
        if($request->isPost()) {
            $formFilter = new \Sound\Form\CommentFormFilter;
            $formFilter->getInputFilter()->get('user_id')->setAllowEmpty(true);
            $data = $request->getPost();
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($data);
            if($form->isValid()) {
                $comment = new \Sound\Model\Dto\CommentDto;
                $comment->exchangeArray($form->getData());
                $comment->created_at = date('Y-m-d H:i:s');
                $comment->updated_at = date('Y-m-d H:i:s');
                $this->getCommentDao()->save($comment);
                $this->flashMessenger()->addMessage('Create comment successfully');
			    return $this->redirect()->toRoute('comment');
            } 
        }
        
        return array(
            'form' => $form,
        );
    }
    
    /**
     * Edit a comment from backend
     * 
     * @return void
     */
    public function editAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('comment');
        }
        $comment = $this->getCommentDao()->fetchOne($id);
        if(!$comment) {
            return $this->redirect()->toRoute('comment');    
        }
        $request = $this->getRequest();
        $form = new \Sound\Form\CommentForm;
        $form->get('user_id')->setOptions(array('value_options' => $this->getUsers()));
        $form->get('sound_id')->setOptions(array('value_options' => $this->getSounds()));
        $form->bind($comment);
        if($request->isPost()) {
            $formFilter = new \Sound\Form\CommentFormFilter;
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()) {
                $comment->updated_at = date('Y-m-d H:i:s');
                $this->getCommentDao()->save($comment);
                //echo '<pre>'; var_dump($comment); echo '</pre>'; die;
                $this->flashMessenger()->addMessage('Edit comment successfully');
			    return $this->redirect()->toRoute('comment');    
            }
        }
        
        return array(
            'form' => $form,
            'id' => $id,
        );
    }
    
    /**
     * Delete a comment from backend
     * 
     * @return
     */
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('comment',array('action' => 'index'));
        }
        $this->getCommentDao()->delete($id);
        
        $this->flashMessenger()->addMessage('Delete comment successfully');
		return $this->redirect()->toRoute('comment');
        
    }
    
    /**
     * Get all sounds and bind to form
     * 
     * @return void
     */
    public function getSounds()
    {
        $sounds = $this->getSoundDao()->fetchAll();
        $sound_array = array();
        foreach($sounds as $sound) {
            $sound_array[$sound->id] = $sound->title;
        }
        return $sound_array;
    }
    
    /**
     * Get all users and bind to form
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