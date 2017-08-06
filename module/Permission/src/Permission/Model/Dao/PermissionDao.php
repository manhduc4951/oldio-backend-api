<?php
namespace Permission\Model\Dao;
use Zend\Db\TableGateway\TableGateway;

class PermissionDao
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
    
    public function save($dto)
    {
        foreach ($dto as $d) {
            $this->tableGateway->insert($d);
        }
    }
    
    public function deleteAll()
    {
        $this->tableGateway->delete();
    }
    
    public function fetchOne($rid,$resourceName)
    {  
        $rowset = $this->tableGateway->select(array('rid' => $rid,'module_controller_action' => $resourceName,'privilege' => 1));
        $row = $rowset->current();
        if (!$row) {
           return false;
        }
        return true;
    }
}