<?php
namespace User\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class FacebookRestController extends AbstractMyRestfulController
{  
    protected $userDao;
    
    protected $followDao;
    
    public function getUserDao()
    {
        if (!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    public function getFollowDao()
    {
        if (!$this->followDao) {
            $sm = $this->getServiceLocator();
            $this->followDao = $sm->get('User\Model\FollowAudienceDao');
        }
        return $this->followDao;
    }
    
    /**
     * Find friends via facebook (facebook id)
     * 
     * @return
     */
    public function getList()
    {
        $facebookId = $this->params()->fromQuery('facebook_id',0);
        $token = $this->params()->fromQuery('token',0);
        if(!($facebookId) || !($token)) {
            return $this->error('Please fill up the parameter',400);
        }
        $user = $this->getUserDao()->fetchOneBy('facebook_id',$facebookId);
        if(!$user) {
            return $this->error('Your facebook id does not exist in my app');
        }
        
        $api = 'https://graph.facebook.com/'.$facebookId.'?fields=id,name,friends.fields(email,first_name,birthday)&access_token='.$token;
        $tuCurl = curl_init($api);
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 1); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
        $tuData = curl_exec($tuCurl);
        $data =  json_decode($tuData);
        //echo '<pre>'; var_dump($data); echo '</pre>'; die;
        if(!empty($data) && isset($data->friends)) {
            $friendIds = array();
            foreach($data->friends->data as $friend) {
                $friendIds[] = $friend->id;
            }
            $friends = $this->getUserDao()->fetchAllByFacebookId($friendIds)->toArray();
            $config = $this->getServiceLocator()->get('config');
            $s3client = $this->getServiceLocator()->get('Aws')->get('S3');
            foreach($friends as &$friend) {
                if($friend['avatar'] && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['user_avatar_path_upload'].'/'.$friend['avatar'])) {
                    $friend['avatar'] = $this->getS3Url($config['config_ica467']['user_avatar_path_upload'].'/'.$friend['avatar']);
                } else {
                    $friend['avatar'] = null;
                }
                if($friend['cover_image'] && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467']['user_cover_path_upload'].'/'.$friend['cover_image'])) {
                    $friend['cover_image'] = $this->getS3Url($config['config_ica467']['user_cover_path_upload'].'/'.$friend['cover_image']);
                } else {
                    $friend['cover_image'] = null;
                }
                if($this->getFollowDao()->fetchOne($friend['id'],$user->id)) {
                    $friend['followed'] = 1;
                } else {
                    $friend['followed'] = 0;    
                }
            }
            return $this->success($friends);    
        } else {
            return $this->error('Unable to get your facebook information, please check the facebook id or access token');
        }
        
    }
    
    public function get($id) {}
    
    public function create($data) {}
    
    public function update($id, $data) {}
    
    public function delete($id) {}
    
    
}
