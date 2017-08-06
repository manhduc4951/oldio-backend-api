<?php
namespace User\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
Use Zend\Db\Sql\Expression;

class FollowAudienceDao
{
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
          $this->tableGateway = $tableGateway;
     }

     /**
      * Check following status: already followed or not yet
      * 
      * @param mixed $userIdAudience
      * @param mixed $userIdFollowing
      * @return
      */
     public function fetchOne($userIdAudience,$userIdFollowing)
     {
          $rowset = $this->tableGateway->select(array('user_id_audience' => $userIdAudience,
                                                      'user_id_following' => $userIdFollowing  
          ));
          $row = $rowset->current();
          if(!$row) {
            return false;
          }
          return $row;
     }
     
     
     
     /**
      * Get list following belong to an user
      * 
      * @param mixed $criteria
      * @param mixed $type
      * @return
      */
     public function fetchUsersFollowing($criteria = array(),$type = null)
     {
          $userIdFollowing = (isset($criteria['user'])) ? $criteria['user'] : 0;
          $limit = (isset($criteria['limit'])) ? $criteria['limit'] : null;
          $offset = (isset($criteria['offset'])) ? $criteria['offset'] : null;
          $updated_at = (isset($criteria['updated_at'])) ? $criteria['updated_at'] : null;
        
          $select = new \Zend\Db\Sql\Select;
          $select->from('following_audience');
          $select->columns(array());
          $select->where(array('following_audience.user_id_following' => $userIdFollowing));
          if($type == 'minimum') {
            $select->columns(array('*'));
            $resultSet = $this->tableGateway->selectWith($select);
            return $resultSet;
          }
          $select->join('user','following_audience.user_id_audience = user.id', array('id','facebook_id','username','display_name','avatar','cover_image','full_name','phone','birthday','gender','country_id','storage_plan_id','description','created_at','updated_at'));
          if(strlen($updated_at)) {
            $select->where->greaterThan('user.updated_at',$updated_at);
          }
          //$select->join('sound',new Expression("sound.user_id = user.id AND sound.type = 1"), array('sounds' => new Expression("COUNT(DISTINCT(sound.id))")),'left');
          $select->join('sound','sound.user_id = user.id', array('sounds' => new Expression("COUNT(DISTINCT(sound.id))")),'left');
          $select->join('like','like.sound_id = sound.id',array('likes' => new Expression("COUNT(DISTINCT(like.id))")),'left');
          $select->join('favorite','favorite.sound_id = sound.id',array('favorites' => new Expression("COUNT(DISTINCT(favorite.id))")),'left');
          $select->join(array('following_audience2' => 'following_audience'),'user.id = following_audience2.user_id_following',array('followings' => new Expression("COUNT(DISTINCT(following_audience2.id))")),'left');
          $select->join(array('following_audience3' => 'following_audience'),'user.id = following_audience3.user_id_audience',array('audiences' => new Expression("COUNT(DISTINCT(following_audience3.id))")),'left');
          
          if(strlen($limit)) {
              $select->limit((int)$limit);    
          }
          if(strlen($limit) && strlen($offset)) {
              $select->limit((int)$limit); 
              $select->offset((int)$offset);    
          }
          $select->group('user.id');
          $resultSet = $this->tableGateway->selectWith($select);
       
          return $resultSet;   
     }
     
     /**
      * Get list audiencer belong to an user
      * 
      * @param mixed $criteria
      * @return
      */
     public function fetchUsersAudience($criteria = array())
     {
          $userIdAudience = (isset($criteria['user'])) ? $criteria['user'] : 0;
          $limit = (isset($criteria['limit'])) ? $criteria['limit'] : null;
          $offset = (isset($criteria['offset'])) ? $criteria['offset'] : null;
          $updated_at = (isset($criteria['updated_at'])) ? $criteria['updated_at'] : null;
        
          $select = new \Zend\Db\Sql\Select;
          $select->from('following_audience');
          $select->columns(array());
          $select->where(array('following_audience.user_id_audience' => $userIdAudience));
          $select->join('user','following_audience.user_id_following = user.id', array('id','facebook_id','username','display_name','avatar','cover_image','full_name','phone','birthday','gender','country_id','storage_plan_id','description','created_at','updated_at'));
          if(strlen($updated_at)) {
            $select->where->greaterThan('user.updated_at',$updated_at);
          }
          //$select->join('sound',new Expression("sound.user_id = user.id AND sound.type = 1"), array('sounds' => new Expression("COUNT(DISTINCT(sound.id))")),'left');
          $select->join('sound','sound.user_id = user.id', array('sounds' => new Expression("COUNT(DISTINCT(sound.id))")),'left');
          $select->join('like','like.sound_id = sound.id',array('likes' => new Expression("COUNT(DISTINCT(like.id))")),'left');
          $select->join('favorite','favorite.sound_id = sound.id',array('favorites' => new Expression("COUNT(DISTINCT(favorite.id))")),'left');
          $select->join(array('following_audience2' => 'following_audience'),'user.id = following_audience2.user_id_following',array('followings' => new Expression("COUNT(DISTINCT(following_audience2.id))")),'left');
          $select->join(array('following_audience3' => 'following_audience'),'user.id = following_audience3.user_id_audience',array('audiences' => new Expression("COUNT(DISTINCT(following_audience3.id))")),'left');
          if(strlen($limit)) {
              $select->limit((int)$limit);    
          }
          if(strlen($limit) && strlen($offset)) {
              $select->limit((int)$limit); 
              $select->offset((int)$offset);    
          }
          $select->group('user.id');
          $resultSet = $this->tableGateway->selectWith($select);
       
          return $resultSet;   
     }

     /**
      * Follow
      * 
      * @param mixed $followAudience
      * @return
      */
     public function save(\User\Model\Dto\FollowAudienceDto $followAudience)
     {  
          $data = $followAudience->getArrayCopy();
          $this->tableGateway->insert($data);
          return $this->tableGateway->lastInsertValue;  
          
     }

     /**
      * Turnoff follow or audience
      * 
      * @param mixed $id
      * @return void
      */
     public function delete($id)
     {
          $this->tableGateway->delete(array('id' => $id));
     }
     
     public function fetchAllBy($column,$value)
     {
        $resultSet = $this->tableGateway->select(array($column => $value));
        return $resultSet;
     }
    
}

