<?php
namespace User\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;

class SoundCategoryDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
         /**
      * Fetch all user have sound which belong to a category
      * 
      * @param mixed $criteria
      * @return
      */
     public function fetchUsersBelongCategory($criteria = array())
     {
          $category_id = (isset($criteria['category_id'])) ? $criteria['category_id'] : 0;
          $limit = (isset($criteria['limit'])) ? $criteria['limit'] : null;
          $offset = (isset($criteria['offset'])) ? $criteria['offset'] : null;
          $updated_at = (isset($criteria['updated_at'])) ? $criteria['updated_at'] : null;          
        
          $select = new \Zend\Db\Sql\Select ;
          $select->from('sound_category');
          $select->columns(array());       
          $select->where(array('sound_category.category_id' => $category_id));
          $select->join('sound','sound_category.sound_id = sound.id',array('user_id' => new Expression("DISTINCT(sound.user_id)")),'inner');
          $select->join('user','sound.user_id = user.id',array('user_id' => 'id','avatar','display_name'),'inner');
          
          if(strlen($updated_at)) {
            $select->where->greaterThan('user.updated_at',$updated_at);
          }
          
          $select->join(array('sound2' => 'sound'),'user.id = sound2.user_id',array('sounds' => new Expression("COUNT(DISTINCT(sound2.id))")),'left');
          $select->join('like','like.sound_id = sound2.id',array('likes' => new Expression("COUNT(DISTINCT(like.id))")),'left');
          $select->join('following_audience','user.id = following_audience.user_id_audience',array('followers' => new Expression("COUNT(DISTINCT(following_audience.id))")),'left');
          if($limit != null) {
              $select->limit((int)$limit);    
          }
          if($limit!=null && $offset != null) {
              $select->limit((int)$limit); 
              $select->offset((int)$offset);    
          }
          $select->order('likes DESC');
          $select->group('user.id');
          $resultSet = $this->tableGateway->selectWith($select);
      
          return $resultSet;
    
     }
     
     public function save(\User\Model\Dto\SoundCategoryDto $soundCategory)
     {  
          $data = $soundCategory->getArrayCopy();
          $this->tableGateway->insert($data); 
          
     }
     
     public function deleteBy($column,$value)
     {
        $this->tableGateway->delete(array($column => $value));
     }
     
     public function fetchAllBy($column,$value)
     {
        $resultSet = $this->tableGateway->select(array($column => $value));
        return $resultSet;
        
     }
    
}

