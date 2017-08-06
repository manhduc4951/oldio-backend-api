<?php
namespace Permission\Model\Dao;
use Zend\Db\TableGateway\TableGateway;

class RoleNameDao
{
    protected $tableGateway;
    
    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function fetchAll($rid = null)
    {
        $select = new \Zend\Db\Sql\Select;
        $select->from('roles');
        if($rid) {
            $select->where->greaterThanOrEqualTo('roles.rid',$rid);    
        }
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
    }
    
}