<?php
namespace Settings\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class StoragePlanDao
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
    
    public function fetchOne($id)
    {
          $id  = (int) $id;
          $rowset = $this->tableGateway->select(array('id' => $id));
          $rowset->setArrayObjectPrototype(new \Settings\Model\Dto\StoragePlanDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
    }
     
}

