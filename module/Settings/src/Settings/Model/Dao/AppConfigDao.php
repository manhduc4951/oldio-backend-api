<?php
namespace Settings\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class AppConfigDao
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
          $rowset->setArrayObjectPrototype(new \Settings\Model\Dto\AppConfigDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
    }
    
    public function save(\Settings\Model\Dto\AppConfigDto $appConfig)
    {  
          $data = $appConfig->getArrayCopy();  
          $id = (int)$appConfig->id;
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
    
    public function delete($id)
    {
        $this->tableGateway->delete(array('id' => $id));
    }  
}

