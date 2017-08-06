<?php
namespace Permission\Model\Dao;
use Zend\Db\Sql\Sql;

class RoleDao extends Sql
{
    protected $table = 'users_roles';
    
    public function __construct($adapter)
    {
        parent::__construct($adapter);
    }
    
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }
    
    public function fetchByUid($uid)
    {
        $select = $this->select()->join(
             'roles',
             'roles.rid = users_roles.rid',
             array('rid', 'role_name' => 'name'), // (optional) list of columns, same requiremetns as columns() above
             'inner' // (optional), one of inner, outer, left, right also represtned by constants in the API
        );
        
        $statement = $this->prepareStatementForSqlObject($select);
        $result = $statement->execute();
        $row = $result->current();
        
        if (!$row) {
            throw new \Exception("Could not find row $uid");
        }
        return $row;
    }
}