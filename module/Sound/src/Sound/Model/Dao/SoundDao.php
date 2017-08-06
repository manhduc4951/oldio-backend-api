<?php
namespace Sound\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class SoundDao
{
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
          $this->tableGateway = $tableGateway;
     }
    
     public function fetchAll()
     {
          $resultSet = $this->tableGateway->select();
          return $resultSet;
     }
     
     /**
      * Get all sounds belong to an user
      * 
      * @param mixed $criteria
      * @return
      */
     public function getList($criteria = array())
     {
        $user_id = (isset($criteria['user_id'])) ? $criteria['user_id'] : 0;
        $type = (isset($criteria['type'])) ? $criteria['type'] : null;
        $limit = (isset($criteria['limit'])) ? $criteria['limit'] : null;
        $offset = (isset($criteria['offset'])) ? $criteria['offset'] : null;
        $updated_at = (isset($criteria['updated_at'])) ? $criteria['updated_at'] : null;
        
        $select = new \Zend\Db\Sql\Select('sound');
        $select->where(array('sound.user_id' => $user_id));
        //if(in_array($type,array(\Sound\Model\Dto\SoundDto::SOUND_TYPE_BROADCAST,\Sound\Model\Dto\SoundDto::SOUND_TYPE_PENDING))) {
        //    $select->where(array('type' => $type));
        //}
        if($type) {
            $select->where(array('type' => $type));
        }
        if(strlen($criteria['updated_at'])) {
            $select->where->greaterThan('sound.updated_at',$updated_at);
        }
        $select->join('like','sound.id = like.sound_id', array('likes' => new Expression("COUNT(DISTINCT(like.id))")),'left');
        $select->join('view','sound.id = view.sound_id', array('viewed' => new Expression("COUNT(DISTINCT(view.id))")),'left');
        $select->join('play','sound.id = play.sound_id', array('played' => new Expression("COUNT(DISTINCT(play.id))")),'left');
        $select->join('comment','sound.id = comment.sound_id', array('comments' => new Expression("COUNT(DISTINCT(comment.id))")),'left');        
        if($limit != null) {
            $select->limit((int)$limit);    
        }
        if($limit!=null && $offset != null) {
            $select->limit((int)$limit); 
            $select->offset((int)$offset);    
        }
        $select->group('sound.id');        
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;  
     }

     /**
      * fetch sound to check sound exist or not
      * 
      * @param mixed $id
      * @return
      */
     public function fetchOne($id)
     {
          $id  = (int) $id;
          $rowset = $this->tableGateway->select(array('id' => $id));
          $rowset->setArrayObjectPrototype(new \Sound\Model\Dto\SoundDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }
     
    
     /**
      * Get detail a sound
      * 
      * @param mixed $id
      * @param mixed $type
      * @return
      */
     public function fetchOneDetail($id,$type = null)
     {
          $id = (int) $id;
          $select = new \Zend\Db\Sql\Select;
          $select->from('sound');
          $select->where(array('sound.id' => $id));
          $select->join('user','sound.user_id = user.id', array('user_id' => 'id','display_name','avatar'));
          $select->join('like','sound.id = like.sound_id', array('likes' => new Expression("COUNT(DISTINCT(like.id))")),'left');
          $select->join('view','sound.id = view.sound_id', array('viewed' => new Expression("COUNT(DISTINCT(view.id))")),'left');
          $select->join('play','sound.id = play.sound_id', array('played' => new Expression("COUNT(DISTINCT(play.id))")),'left');
          if($type == 'backend') {
            $select->join('comment','sound.id = comment.sound_id', array('comments' => new Expression("COUNT(DISTINCT(comment.id))")),'left');
            //$select->join('category','sound.category_id = category.id',array('category_name' => 'name'),'inner');  
          }
          $resultSet = $this->tableGateway->selectWith($select)->current();
      
          return $resultSet;
     }
     
     /**
      * Get news sound from following list of an user (home screen)
      * 
      * @param mixed $criteria
      * @return void
      */
     public function getNewsFeed($criteria = array())
     {
          $userIds = (isset($criteria['users'])) ? $criteria['users'] : array();
          $limit = (isset($criteria['limit'])) ? $criteria['limit'] : null;
          $offset = (isset($criteria['offset'])) ? $criteria['offset'] : null;
          //$updated_at = (isset($criteria['updated_at'])) ? $criteria['updated_at'] : null;
          $timeFrom = (isset($criteria['time_from'])) ? $criteria['time_from'] : null;
          $timeTo = (isset($criteria['time_to'])) ? $criteria['time_to'] : null;
          
          $select = new \Zend\Db\Sql\Select;
          $select->from('sound');
          $select->columns(array('id','user_id','title','thumbnail','thumbnail2','thumbnail3','description','sound_path','duration','type','created_at','updated_at'));
          $select->where->in('sound.user_id',$userIds);
          if(strlen($timeFrom)) {
            $select->where->greaterThan('sound.created_at',$timeFrom);
          }
          if(strlen($timeTo)) {
            $select->where->lessThanOrEqualTo('sound.created_at',$timeTo);
          }
          $select->join('like','sound.id = like.sound_id', array('likes' => new Expression("COUNT(DISTINCT(like.id))")),'left');
          $select->join('view','sound.id = view.sound_id', array('viewed' => new Expression("COUNT(DISTINCT(view.id))")),'left');
          $select->join('play','sound.id = play.sound_id', array('played' => new Expression("COUNT(DISTINCT(play.id))")),'left');
          $select->join('comment','sound.id = comment.sound_id', array('comments' => new Expression("COUNT(DISTINCT(comment.id))")),'left');        
          $select->join('user','user.id = sound.user_id',array('username','display_name','avatar'),'inner');
          if(strlen($limit)) {
              $select->limit((int)$limit);    
          }
          if(strlen($limit) && strlen($offset)) {
              $select->limit((int)$limit); 
              $select->offset((int)$offset);    
          }
          $select->group('sound.id');
          $select->order('sound.created_at DESC');
          $select->order('sound.id DESC');
          
          $resultSet = $this->tableGateway->selectWith($select);
          return $resultSet;
     }

     /**
      * Save sound into db
      * 
      * @param mixed $sound
      * @return
      */
     public function save(\Sound\Model\Dto\SoundDto $sound)
     {  
          $data = $sound->getArrayCopy();  
          $id = (int)$sound->id;
          if ($id == 0) {
               $this->tableGateway->insert($data);
               return $this->tableGateway->lastInsertValue;
          } else {
               if ($this->fetchOne($id)) {
                    $this->tableGateway->update($data, array('id' => $id));
               } else {
                    return false;
               }
          }
     }

     /**
      * Delete sound
      * 
      * @param mixed $id
      * @return void
      */
     public function delete($id)
     {
          $this->tableGateway->delete(array('id' => $id));
     }

     
     /**
      * Search sounds by title
      * 
      * @param mixed $filter
      * @return void
      */
     public function searchSound($filter,$column = 'title')
     {
        $select = new \Zend\Db\Sql\Select;
        $select->from('sound');
        if($column == 'title') {
            $select->where->like('sound.title',"%$filter%");    
        } elseif($column == 'tag') {
            $select->where->like('sound.tags',"%$filter%");    
        }
        $select->join('like','sound.id = like.sound_id', array('likes' => new Expression("COUNT(DISTINCT(like.id))")),'left');
        $select->join('view','sound.id = view.sound_id', array('viewed' => new Expression("COUNT(DISTINCT(view.id))")),'left');
        $select->join('play','sound.id = play.sound_id', array('played' => new Expression("COUNT(DISTINCT(play.id))")),'left');
        $select->join('comment','sound.id = comment.sound_id', array('comments' => new Expression("COUNT(DISTINCT(comment.id))")),'left');
        $select->join('user','sound.user_id = user.id',array('username','display_name','avatar'),'inner');
        $select->group('sound.id');
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
        
     }
     
     /**
      * Get tags 
      * 
      * @param mixed $tag
      * @return void
      */
     public function fetchTags($tag)
     {
        $select = new \Zend\Db\Sql\Select;
        $select->columns(array(new Expression('DISTINCT(tags) as tags')));
        $select->from('sound');
        $select->where->like('tags',"%$tag%");
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;  
     }
     
     public function fetchAllBy($column,$value)
     {
        $resultSet = $this->tableGateway->select(array($column=>$value));
        return $resultSet;
     }
     
     /**
      * Get all private sounds of an user
      * 
      * @param mixed $userId
      * @return void
      */
     public function fetchPrivateSounds($userId)
     {
        $select = new \Zend\Db\Sql\Select;
        $select->from('sound');
        $select->where(array('user_id' => $userId, 'type' => \Sound\Model\Dto\SoundDto::SOUND_TYPE_PENDING));
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
        
     }
     
     /**
      * Get all sound with paginator and filter query (for backend)
      * 
      * @return void
      */
     public function fetchAllSound($paginated=false,$query=null)
     {
        if($paginated) {
             $select = ($query) ? $this->search($query) : new \Zend\Db\Sql\Select('sound');
             $select->join('user', 'sound.user_id = user.id', array('display_name','username','full_name'), 'inner');
             $select->join('view','sound.id = view.sound_id', array('viewed' => new Expression("COUNT(DISTINCT(view.id))")),'left');
             $select->join('play','sound.id = play.sound_id', array('played' => new Expression("COUNT(DISTINCT(play.id))")),'left');
             //$select->join('category','sound.category_id = category.id',array('category_name' => 'name'),'inner');
             $select->group('sound.id');
             $select->order('sound.updated_at DESC');
             $paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter());
             $paginator = new Paginator($paginatorAdapter);
             
             return array(
                'paginator' => $paginator,
                'query' => $query,
             );
         }
         $resultSet = $this->tableGateway->select();
         
         return $resultSet;
     }
     
     public function search($criteria = array())
     { 
        $select = new \Zend\Db\Sql\Select('sound');

        if(!empty($criteria['sound_ids'])) {
            $select->where->in('sound.id',$criteria['sound_ids']);
        }
        if(!empty($criteria['username'])) {
            $select->where->AND->NEST->like('username',"%$criteria[username]%")
                                 ->OR->like('display_name',"%$criteria[username]%")
                                 ->OR->like('full_name',"%$criteria[username]%")->UNNEST; 
        }
        if(!empty($criteria['title'])) {
            $select->where->like('title',"%$criteria[title]%");    
        }
        if(!empty($criteria['type'])) {
            $select->where->in('type',$criteria['type']);
        }
        if(!empty($criteria['connect_facebook'])) {
            $select->where->in('connect_facebook',$criteria['connect_facebook']);
        }
        if(!empty($criteria['connect_twitter'])) {
            $select->where->in('connect_twitter',$criteria['connect_twitter']);
        }
        if(!empty($criteria['created_at_from'])) {
            $select->where->greaterThanOrEqualTo('sound.created_at',$criteria['created_at_from']);
        }
        if(!empty($criteria['created_at_to'])) {
            $select->where->lessThanOrEqualTo('sound.created_at',$criteria['created_at_to']);
        }
        
        return $select;
     }
    
}

