<?php
namespace User\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class CategoryDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Get all categories in application
     * 
     * @return
     */
    public function fetchAll()
    {
          $resultSet = $this->tableGateway->select();
          return $resultSet;
    }

    /**
     * Fetch db to check category exist or not
     * 
     * @param mixed $id
     * @return
     */
    public function fetchOne($id)
    {
          $id  = (int) $id;
          $rowset = $this->tableGateway->select(array('id' => $id));
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
    }
    
    /**
     * Get all categories have id in array
     * 
     * @param mixed $ids
     * @return
     */
    public function fetchAllInArray($ids)
    {
        $select = new \Zend\Db\Sql\Select;
        $select->from('category');
        $select->where->in('id',$ids);
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
       
}

