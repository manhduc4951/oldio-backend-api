<?php
namespace SoundSet\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;
use Zend\Db\Sql\Predicate\NotIn;

class SoundSetDao
{
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }
    
     public function fetchAll($criteria = array())
     {
        $limit = (isset($criteria['limit'])) ? $criteria['limit'] : null;
        $offset = (isset($criteria['offset'])) ? $criteria['offset'] : null;
        $updated = (isset($criteria['updated_at'])) ? $criteria['updated_at'] : null;
        $price = (isset($criteria['price'])) ? $criteria['price'] : null;
        
        $select = new \Zend\Db\Sql\Select;
        $select->from('sound_set');
        if(strlen($updated)) {
            $select->where->greaterThan('sound_set.updated_at',$updated);
        }
        if(strlen($limit)) {
            $select->limit($limit);
        }
        if(strlen($limit) && strlen($offset)) {
            $select->limit($limit);
            $select->offset($offset);
        }
        if(strlen($price)) {
            $select->where->lessThanOrEqualTo('price',$price);
        }
        $select->order('sound_set.updated_at DESC');
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
     }

     
     public function fetchOne($id)
     {
          $id  = (int) $id;
          $rowset = $this->tableGateway->select(array('id' => $id));
          $rowset->setArrayObjectPrototype(new \SoundSet\Model\Dto\SoundSetDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }
     
     /**
      * Get all soundsets are free and have not yet in the list soundboard of an user
      * 
      * @param mixed $soundSetIds
      * @return
      */
//     public function fetchFreeSoundSet($soundSetIds)
//     {
//          $select = new \Zend\Db\Sql\Select;
//          $select->from('sound_set');
//          $select->where->lessThanOrEqualTo('price',0);
//          $select->where->addPredicate(new NotIn('id',$soundSetIds));
//          $resultSet = $this->tableGateway->selectWith($select);
//          
//          return $resultSet;
//     }
     
     public function save(\SoundSet\Model\Dto\SoundSetDto $soundSet)
     {  
          $data = $soundSet->getArrayCopy();
          $id = (int)$soundSet->id;
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
      * Get all sound set with paginator and filter query (for backend)
      * 
      * @return void
      */
     public function fetchAllSoundSet($paginated=false,$query=null)
     {
        if($paginated) {
             $select = ($query) ? $this->search($query) : new \Zend\Db\Sql\Select('sound_set');
             //$select->join('user', 'sound.user_id = user.id', array('display_name','username','full_name'), 'inner');
             //$select->join('category','sound.category_id = category.id',array('category_name' => 'name'),'inner');
             $select->order('sound_set.updated_at DESC');
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
        $select = new \Zend\Db\Sql\Select('sound_set');
        
        if(!empty($criteria['name'])) {
            $select->where->like('sound_set.name',"%$criteria[name]%");
        }
        if(!empty($criteria['description'])) {
            $select->where->like('sound_set.description',"%$criteria[description]%");    
        }
        if(!empty($criteria['creation'])) {
            $select->where->like('sound_set.creation',"%$criteria[creation]%");
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
     
     public function delete($id)
     {
        $this->tableGateway->delete(array('id' => $id));
     }
     
     
}

