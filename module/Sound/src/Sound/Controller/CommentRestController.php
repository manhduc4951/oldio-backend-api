<?php
namespace Sound\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class CommentRestController extends AbstractMyRestfulController
{  
    protected $commentDao;
    
    protected $soundDao;
    
    protected $settingsBusiness;
    
    public function getCommentDao()
    {
        if (!$this->commentDao) {
            $sm = $this->getServiceLocator();
            $this->commentDao = $sm->get('Sound\Model\CommentDao');
        }
        return $this->commentDao;
    }
    
    public function getSoundDao()
    {
        if (!$this->soundDao) {
            $sm = $this->getServiceLocator();
            $this->soundDao = $sm->get('Sound\Model\SoundDao');
        }
        return $this->soundDao;
    }
    
    public function getSettingsBusiness()
    {
        if(!$this->settingsBusiness) {
            $sm = $this->getServiceLocator();
            $this->settingsBusiness = $sm->get('Settings\Business\SettingsBussiness');
        }
        return $this->settingsBusiness;
    }

    /**
     * Get all comments of a sound
     * 
     * @return
     */
    public function getList()
    {
        $sound_id = (int) $this->params()->fromQuery('sound_id',0);
        $limit = $this->params()->fromQuery('limit',null);
        $offset = $this->params()->fromQuery('offset',null);
        $updated_at = $this->params()->fromQuery('updated_at',null);
        
        if (!$this->getSoundDao()->fetchOne($sound_id)) {
            return $this->error('The sound you request does not exist !');
        }
        
        $criteria = array(
            'sound_id' => $sound_id,
            'limit' => $limit,
            'offset' => $offset,
            'updated_at' => $updated_at,
        );
        $comments = $this->getCommentDao()->getSoundComments($criteria);
        $filePaths = array('avatar' => 'user_avatar_path_upload');
        $comments = $this->getObjectsUrl($comments,$filePaths);
        
        return $this->success(array(
            'total_comments' => count($comments),
            'comments' => $comments,    
        ));
        
    }

    /**
     * Post a comment
     * 
     * @param mixed $data
     * @return
     */
    public function create($data)
    { 
        $userId = $this->identity()->id;
        $form = new \Sound\Form\CommentForm;
        $commentFormFilter = new \Sound\Form\CommentFormFilter;
        $form->setInputFilter($commentFormFilter->getInputFilter());        
        $form->setData($data);
        if ($form->isValid()) {
            $dataForm = $form->getData();
            if (!$this->getSoundDao()->fetchOne($dataForm['sound_id'])) {
                return $this->error('The sound you request does not exist !');     
            }
            $comment = new \Sound\Model\Dto\CommentDto;
            $comment->exchangeArray($dataForm);
            $comment->user_id = $userId;
            $comment->created_at = date('Y-m-d H:i:s');
            $comment->updated_at = date('Y-m-d H:i:s');
            $commentId = $this->getCommentDao()->save($comment);
            
            $this->getSettingsBusiness()->push(array(
                'type' => \Settings\Model\Dto\SettingsDto::PUSH_COMMENT_YOUR_POST,
                'id' => $comment->sound_id,
                'user_id' => $userId,
            ));
            
            return $this->success(array('id' => $commentId));
        } else {
			//echo '<pre>'; var_dump($form->getMessages()); echo '</pre>'; die;
			
            return $this->formInvalid();
                    
        }

    }

    /**
     * Delete a comment
     * 
     * @param mixed $id
     * @return
     */
    public function delete($id)
    {  
        $user_id = $this->identity()->id;
        
        $comment = $this->getCommentDao()->fetchOne($id);
        if(!$comment) {
            return $this->error('The comment you request does not exist');
        }
        
        if ($comment->user_id != $user_id) {
            $this->error('You can not delete the comment which does not belong to you',400);
        }
        
        $this->getCommentDao()->delete($id);
        return $this->success();
        
    }
    
    public function get($id) {}
    
    public function update($id, $data) {}

}
