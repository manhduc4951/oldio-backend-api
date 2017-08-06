<?php
namespace Sound\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;

class FavoriteDao
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
      * Get all sounds are in favorite of an user
      * 
      * @param mixed $criteria
      * @return
      */
     public function fetchFavoriteSounds($criteria = array())
     {
        $userId = (isset($criteria['user'])) ? $criteria['user'] : 0;
        $limit = (isset($criteria['limit'])) ? $criteria['limit'] : null;
        $offset = (isset($criteria['offset'])) ? $criteria['offset'] : null;
        $updated_at = (isset($criteria['updated_at'])) ? $criteria['updated_at'] : null;
        
        $select = new \Zend\Db\Sql\Select;
        $select->from('favorite');
        $select->where(array('favorite.user_id' => $userId));
        $select->columns(array('created_at'));
        $select->join('sound','sound.id = favorite.sound_id',array('user_id','title','thumbnail','thumbnail2','thumbnail3','id','description','sound_path','type','duration','connect_facebook','connect_twitter','tags'),'inner');
        $select->join('like','sound.id = like.sound_id', array('likes' => new Expression("COUNT(DISTINCT(like.id))")),'left');
        $select->join('view','sound.id = view.sound_id', array('viewed' => new Expression("COUNT(DISTINCT(view.id))")),'left');
        $select->join('play','sound.id = play.sound_id', array('played' => new Expression("COUNT(DISTINCT(play.id))")),'left');
        if(strlen($updated_at)) {
            $select->where->greaterThan('sound.updated_at',$updated_at);
        }
        $select->join('user','user.id = favorite.user_id',array('display_name'),'inner');
        if(strlen($limit)) {
            $select->limit((int)$limit);    
        }
        if(strlen($limit) && strlen($offset)) {
            $select->limit((int)$limit); 
            $select->offset((int)$offset);    
        }
        $select->order('favorite.order ASC');
        $select->group('sound.id'); 
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet; 
     }

     /**
      * Check sound is in favorite list or not
      * 
      * @param mixed $soundId
      * @param mixed $userId
      * @return
      */
     public function fetchOne($soundId,$userId)
     {   
          $rowset = $this->tableGateway->select(array('sound_id' => $soundId,'user_id' => $userId));
          $rowset->setArrayObjectPrototype(new \Sound\Model\Dto\FavoriteDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }

     /**
      * Save sound favorite
      * 
      * @param mixed $favorite
      * @return void
      */
     public function save(\Sound\Model\Dto\FavoriteDto $favorite)
     {  
          $data = $favorite->getArrayCopy();
          $this->tableGateway->insert($data);
          $id = $this->tableGateway->lastInsertValue;
          $this->tableGateway->update(array('order' => $id), array('id' => $id));
     }
     
     /**
      * Update favorite list of an user
      * 
      * @param mixed $userSoundSet
      * @return void
      */
     public function update(\Sound\Model\Dto\FavoriteDto $favorite)
     {
        $data = $favorite->getArrayCopy();
        $id = (int) $favorite->id;
        $this->tableGateway->update($data, array('id' => $id));
     }

     /**
      * Remove sound from favorite list
      * 
      * @param mixed $soundId
      * @param mixed $userId
      * @return void
      */
     public function delete($soundId,$userId)
     {
          $this->tableGateway->delete(array('sound_id' => $soundId,'user_id' => $userId));
     }
     
     public function deleteBy($column,$value)
     {
          $this->tableGateway->delete(array($column=>$value));
     }
    
}

