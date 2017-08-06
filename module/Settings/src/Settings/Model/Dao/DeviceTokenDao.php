<?php
namespace Settings\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class DeviceTokenDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAllBy($column,$value)
    {
          $resultSet = $this->tableGateway->select(array($column => $value));
          return $resultSet;
    }
    
    public function fetchOne($userId,$deviceToken)
    {
        $rowset = $this->tableGateway->select(array('user_id' => $userId,'device_token' => $deviceToken));
        $rowset->setArrayObjectPrototype(new \Settings\Model\Dto\DeviceTokenDto);
        $row = $rowset->current();
        if(!$row) {
            return false;
        }
        return $row;
    }
    
    public function save(\Settings\Model\Dto\DeviceTokenDto $deviceToken)
    {  
          $data = $deviceToken->getArrayCopy();  
          $this->tableGateway->insert($data);
    }
    
    public function delete($userId,$deviceToken)
    {
        $this->tableGateway->delete(array('user_id' => $userId,'device_token' => $deviceToken));
    }  
}

