<?php
namespace Sound\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class CommentDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
     /**
      * Fetch all comments
      * 
      * @return
      */
     public function fetchAll()
     {
          $resultSet = $this->tableGateway->select();
          return $resultSet;
     }
     
     /**
      * Fetch all comments by a specific column
      * 
      * @param mixed $name
      * @param mixed $value
      * @return
      */
     public function fetchAllBy($name,$value)
     {
          $resultSet = $this->tableGateway->select(array($name => $value));
          return $resultSet;
     }
    
     /**
      * Get all comments belong to a sound
      * 
      * @param mixed $criteria
      * @return
      */
     public function getSoundComments($criteria = array())
     { 
        $sound_id = (isset($criteria['sound_id'])) ? $criteria['sound_id'] : 0;
        $limit = (isset($criteria['limit'])) ? $criteria['limit'] : null;
        $offset = (isset($criteria['offset'])) ? $criteria['offset'] : null;
        $updated_at = (isset($criteria['updated_at'])) ? $criteria['updated_at'] : null;
        
        $select = new \Zend\Db\Sql\Select ;
        $select->from('comment');       
        $select->where(array('sound_id' => $sound_id));
        if(strlen($updated_at)) {
            $select->where->greaterThan('comment.updated_at',$updated_at);
        }      
        $select->join('user', "user.id = comment.user_id", array('username','display_name','avatar'), 'inner');
        if(strlen($limit)) {
            $select->limit((int)$limit);    
        }
        if(strlen($limit) && strlen($offset)) {
            $select->limit((int)$limit); 
            $select->offset((int)$offset);    
        }        
        $select->order(array('comment.id desc'));   
        $resultSet = $this->tableGateway->selectWith($select);
      
        return $resultSet;
     }

     /**
      * Fetch one comment
      * 
      * @param mixed $id
      * @return
      */
     public function fetchOne($id)
     {
          $id  = (int) $id;
          $rowset = $this->tableGateway->select(array('id' => $id));
          $rowset->setArrayObjectPrototype(new \Sound\Model\Dto\CommentDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }

     /**
      * Save comment to db
      * 
      * @param mixed $comment
      * @return
      */
     public function save(\Sound\Model\Dto\CommentDto $comment)
     {  
          $data = $comment->getArrayCopy();  
          $id = (int)$comment->id;
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
      * Delete comment
      * 
      * @param mixed $id
      * @return void
      */
     public function delete($id)
     {
          $this->tableGateway->delete(array('id' => $id));
     }
     
     /**
      * Delete comment by a specific column
      * 
      * @param mixed $column
      * @param mixed $value
      * @return void
      */
     public function deleteBy($column,$value)
     { 
        $this->tableGateway->delete(array($column => $value));
     }
     
     /**
      * Get all comments with paginator and filter query (for backend)
      * 
      * @return void
      */
     public function fetchAllComment($paginated=false,$query=null)
     {
        if($paginated) {
             $select = ($query) ? $this->search($query) : new \Zend\Db\Sql\Select('comment');
             $select->join('user','comment.user_id = user.id',array('username','display_name','full_name'),'inner');
             $select->join('sound','comment.sound_id = sound.id',array('title'),'inner');
             $select->order('comment.updated_at DESC');
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
        $select = new \Zend\Db\Sql\Select('comment');
        
        if(!empty($criteria['username'])) {
            $select->where->AND->NEST->like('username',"%$criteria[username]%")
                                 ->OR->like('display_name',"%$criteria[username]%")
                                 ->OR->like('full_name',"%$criteria[username]%")->UNNEST;
            
        }
        if(!empty($criteria['comment'])) {
            $select->where->like('comment',"%$criteria[comment]%");
        }
        if(!empty($criteria['sound'])) {
            $select->where->like('sound.title',"%$criteria[sound]%");
        }
        if(!empty($criteria['created_at_from'])) {
            $select->where->greaterThanOrEqualTo('comment.created_at',$criteria['created_at_from']);
        }
        if(!empty($criteria['created_at_to'])) {
            $select->where->lessThanOrEqualTo('comment.created_at',$criteria['created_at_to']);
        }
        
        return $select;
     }
    
}

