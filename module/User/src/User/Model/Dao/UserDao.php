<?php
namespace User\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
Use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Predicate\NotIn;

class UserDao
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
     * Get all users are friend of request user's facebook id
     * 
     * @param mixed $facebookIds
     * @return
     */
    public function fetchAllByFacebookId($facebookIds = array())
    {
        $select = new \Zend\Db\Sql\Select;
        $select->from('user');
        $select->where->in('facebook_id',$facebookIds);
        $select->join('sound','user.id = sound.user_id',array('sounds' => new Expression("COUNT(DISTINCT(sound.id))")),'left');
        $select->join('following_audience','user.id = following_audience.user_id_audience',array('followers' => new Expression("COUNT(DISTINCT(following_audience.id))")),'left');
        $select->group('user.id');
        $resultSet = $this->tableGateway->selectWith($select);
       
        return $resultSet;
        
    }

     /**
      * Check user exist or not
      * 
      * @param mixed $id
      * @return
      */
     public function fetchOne($id)
     {
          $id  = (int) $id;
          $rowset = $this->tableGateway->select(array('id' => $id));
          $rowset->setArrayObjectPrototype(new \User\Model\Dto\UserDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }
     
     /**
      * Get an user by specific column
      * 
      * @param mixed $column
      * @param mixed $value
      * @return
      */
     public function fetchOneBy($column,$value) {
        $rowset = $this->tableGateway->select(array($column => $value));
        $rowset->setArrayObjectPrototype(new \User\Model\Dto\UserDto);
        $row = $rowset->current();
        if (!$row) {
            return false;
        }
        return $row;
        
     }
     
     /**
      * Get detail an user
      * 
      * @param mixed $id
      * @return
      */
     public function fetchOneDetail($id, array $criteria = array())
     {
        $countPrivate = (isset($criteria['count_private'])) ? $criteria['count_private'] : null;
        
        $select = new \Zend\Db\Sql\Select;
        $select->from('user');
        $select->where(array('user.id' => $id));
        //$select->columns(array('uid','username'));
        if($countPrivate) {
            $select->join('sound',new Expression("user.id = sound.user_id AND sound.type = ".\Sound\Model\Dto\SoundDto::SOUND_TYPE_BROADCAST),array('sounds' => new Expression("COUNT(DISTINCT(sound.id))")),'left');    
        } else {
            $select->join('sound','user.id = sound.user_id',array('sounds' => new Expression("COUNT(DISTINCT(sound.id))")),'left');    
        }    
        $select->join('favorite','favorite.sound_id = sound.id',array('favorites' => new Expression("COUNT(DISTINCT(favorite.id))")),'left');
        $select->join('like','like.sound_id = sound.id',array('likes' => new Expression("COUNT(DISTINCT(like.id))")),'left');
        
        $select->join('following_audience','user.id = following_audience.user_id_following',array('followings' => new Expression("COUNT(DISTINCT(following_audience.id))")),'left');
        $select->join(array('following_audience2' => 'following_audience'),'user.id = following_audience2.user_id_audience',array('audiences' => new Expression("COUNT(DISTINCT(following_audience2.id))")),'left');
        //$select->join('favorite','user.id = favorite.user_id',array('favorites' => new Expression("COUNT(DISTINCT(favorite.sound_id))")),'left');
        
        $select->group('user.id');
       
        $resultSet = $this->tableGateway->selectWith($select)->current();
       
        return $resultSet;
     }
     
     
     /**
      * Search user
      * 
      * @param mixed $filter
      * @return
      */
     public function searchUser($filter,array $criteria = array())
     {
        $userId = (isset($criteria['user_id'])) ? $criteria['user_id'] : null;
        
        $select = new \Zend\Db\Sql\Select;
        $select->from('user');
        if($userId) {
            $select->where->addPredicate(new NotIn('user.id',$userId));    
        }
        $select->where->AND->NEST->like('display_name',"%$filter%")->OR->like('full_name',"%$filter%");
        //$select->join('sound',new Expression("user.id = sound.user_id AND sound.type = 1"),array('sounds' => new Expression("COUNT(DISTINCT(sound.id))")),'left');
        $select->join('sound','user.id = sound.user_id',array('sounds' => new Expression("COUNT(DISTINCT(sound.id))")),'left');
        $select->join('following_audience','user.id = following_audience.user_id_audience',array('followers' => new Expression("COUNT(DISTINCT(following_audience.id))")),'left');
        $select->order('display_name ASC');
        $select->group('user.id');
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
     }

     /**
      * Create and update an user
      * 
      * @param mixed $user
      * @return
      */
     public function save(\User\Model\Dto\UserDto $user)
     {
          $data = $user->getArrayCopy();  
          $id = (int)$user->id;
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
      * Delete user
      * 
      * @param mixed $id
      * @return void
      */
     public function delete($id)
     {
          $this->tableGateway->delete(array('id' => $id));
     }
     
     /**
      * Get all user with paginator and filter query (for backend)
      * 
      * @return void
      */
     public function fetchAllUser($paginated=false,$query=null)
     {
        if($paginated) {
             $select = ($query) ? $this->search($query) : new \Zend\Db\Sql\Select('user');
             $select->join('country','user.country_id=country.code',array('country_name' => 'name'),'left');
             $select->order('user.updated_at DESC');
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
     
     public function search($criteria)
     {
        $select = new \Zend\Db\Sql\Select('user');
        
        if(!empty($criteria['username'])) {
            $select->where->like('username',"%$criteria[username]%");    
        }
        if(!empty($criteria['display_name'])) {
            $select->where->like('display_name',"%$criteria[display_name]%");    
        }
        if(!empty($criteria['full_name'])) {
            $select->where->like('full_name',"%$criteria[full_name]%");    
        }
        if(!empty($criteria['gender'])) {
            $select->where->in('gender',$criteria['gender']);
        }
        if(!empty($criteria['country_id'])) {
            $select->where(array('country_id' => $criteria['country_id']));
        }
        if(!empty($criteria['created_at_from'])) {
            $select->where->greaterThanOrEqualTo('user.created_at',$criteria['created_at_from']);
        }
        if(!empty($criteria['created_at_to'])) {
            $select->where->lessThanOrEqualTo('user.created_at',$criteria['created_at_to']);
        }
        if(!empty($criteria['birthday_from'])) {
            $select->where->greaterThanOrEqualTo('user.birthday',$criteria['birthday_from']);
        }
        if(!empty($criteria['birthday_to'])) {
            $select->where->lessThanOrEqualTo('user.birthday',$criteria['birthday_to']);
        }
        
        return $select;
        
     }
    
}

