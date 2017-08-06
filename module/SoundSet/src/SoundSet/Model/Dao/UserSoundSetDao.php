<?php
namespace SoundSet\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;


class UserSoundSetDao
{
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }
     
     /**
      * Get list sound set of an user
      * 
      * @param mixed $criteria
      * @return void
      */
     public function fetchAll($criteria = array())
     { 
        $userId = (isset($criteria['user_id'])) ? $criteria['user_id'] : null;
        $select = new \Zend\Db\Sql\Select;
        $select->from('user_sound_set');
        $select->where(array('user_id' => $userId));
        $select->join('sound_set','user_sound_set.sound_set_id = sound_set.id',array('name','description','image','price','creation'),'inner');
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
        
     }
     
     public function fetchAllBy($column,$value)
     {
        $resultSet = $this->tableGateway->select(array($column => $value));
        return $resultSet;
     }
     
     /**
      * Get one soundset of an user
      * 
      * @return void
      */
     public function fetchOneUserSoundSet($criteria = array())
     {
        $userId = (isset($criteria['user_id'])) ? $criteria['user_id'] : null;
        $soundSetId = (isset($criteria['sound_set_id'])) ? $criteria['sound_set_id'] : null;
        $select = new \Zend\Db\Sql\Select;
        $select->from('user_sound_set');
        $select->where(array('sound_set_id' => $soundSetId, 'user_id' => $userId));
        $select->join('sound_set','user_sound_set.sound_set_id = sound_set.id',array('name','description','image','price','creation'),'inner');
        $resultSet = $this->tableGateway->selectWith($select);
        $row = $resultSet->current();
        
        return $row;
        
     }
     
     /**
      * Delete sound set from sound board of an user
      * 
      * @param mixed $soundSetId
      * @param mixed $userId
      * @return void
      */
     public function delete($soundSetId,$userId)
     {
        $this->tableGateway->delete(array('sound_set_id' => $soundSetId,'user_id' => $userId));
     }
     
     /**
      * Check the exist of an sound set in list sound set of an user
      * 
      * @param mixed $soundSetId
      * @param mixed $userId
      * @return void
      */
     public function fetchOne($soundSetId,$userId)
     {
          $soundSetId = (int) $soundSetId;
          $userId = (int) $userId;
          $rowset = $this->tableGateway->select(array('sound_set_id' => $soundSetId, 'user_id' => $userId));
          $rowset->setArrayObjectPrototype(new \SoundSet\Model\Dto\UserSoundSetDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }
     
     /**
      * Purchase sound set
      * 
      * @param mixed $userSoundSet
      * @return void
      */
     public function save(\SoundSet\Model\Dto\UserSoundSetDto $userSoundSet)
     {  
          $data = $userSoundSet->getArrayCopy();
          $this->tableGateway->insert($data);
          $id = $this->tableGateway->lastInsertValue;
          $this->tableGateway->update(array('order' => $id), array('id' => $id));
     }
     
     /**
      * Update sound set in a sound board of an user
      * 
      * @param mixed $userSoundSet
      * @return void
      */
     public function update(\SoundSet\Model\Dto\UserSoundSetDto $userSoundSet)
     {
        $data = $userSoundSet->getArrayCopy();
        $id = (int) $userSoundSet->id;
        $this->tableGateway->update($data, array('id' => $id));
     }
     
     /**
      * Get soundset purchase of users with paginator and filter query (for backend)
      * 
      * @return void
      */
     public function fetchAllUserSoundSet($paginated=false,$query=null)
     {
        if($paginated) {
             $select = ($query) ? $this->search($query) : new \Zend\Db\Sql\Select('user_sound_set');
             $select->join('sound_set','user_sound_set.sound_set_id = sound_set.id',array('sound_set_name' => 'name','sound_set_image' => 'image','price','creation'),'inner');
             $select->join('user','user_sound_set.user_id = user.id',array('display_name','username','user_avatar' => 'avatar'),'inner');
             $select->order('user_sound_set.created_at DESC');
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
        $select = new \Zend\Db\Sql\Select('user_sound_set');
        
        if(!empty($criteria['name'])) {
            $select->where->like('sound_set.name',"%$criteria[name]%");
        }
        if(!empty($criteria['user'])) {
            $select->where->AND->NEST->like('user.username',"%$criteria[user]%")
                                 ->OR->like('user.display_name',"%$criteria[user]%")
                                 ->OR->like('user.full_name',"%$criteria[user]%")->UNNEST; 
            
        }
        if(!empty($criteria['created_at_from'])) {
            $select->where->greaterThanOrEqualTo('sound_set.created_at',$criteria['created_at_from']);
        }
        if(!empty($criteria['created_at_to'])) {
            $select->where->lessThanOrEqualTo('sound_set.created_at',$criteria['created_at_to']);
        }
        if(is_numeric($criteria['price_from'])) {
            $decimal = number_format($criteria['price_from'],2,'.','');
            $select->where->greaterThanOrEqualTo('sound_set.price',$decimal);
        }
        if(is_numeric($criteria['price_to'])) {
            $decimal = number_format($criteria['price_to'],2,'.','');
            $select->where->lessThanOrEqualTo('sound_set.price',$decimal);
        }
        
        return $select;
     }
    
     
}

