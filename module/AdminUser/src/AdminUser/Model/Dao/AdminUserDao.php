<?php
namespace AdminUser\Model\Dao;

use Zend\Db\TableGateway\TableGateway;
Use Zend\Db\Sql\Expression;
use Zend\Paginator\Adapter\DbSelect;
use Zend\Paginator\Paginator;

class AdminUserDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
//    public function fetchAll()
//     {
//          $resultSet = $this->tableGateway->select();
//          return $resultSet;
//     }

     public function fetchOne($uid)
     {
          $uid  = (int) $uid;
          $rowset = $this->tableGateway->select(array('uid' => $uid));
          $rowset->setArrayObjectPrototype(new \AdminUser\Model\Dto\AdminUserDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }
     
     /**
      * Get info of an user to storage session
      * 
      * @param mixed $uid
      * @return
      */
     public function getUserInfo($uid)
     {
        $select = new \Zend\Db\Sql\Select ;
        $select->from('admin_user');
        $select->columns(array('uid','username','password','status','count' => new Expression("count(*)") ));          
        $select->where(array('admin_user.uid' => $uid));
        $select->join('users_roles', "admin_user.uid = users_roles.uid", array('rid'), 'inner');
        $select->join('roles', "users_roles.rid = roles.rid", array('role_name' => 'name'), 'inner');
        $resultSet = $this->tableGateway->selectWith($select)->current();
        
        return $resultSet; 
     }
     
     /**
      * Get all admin user with paginator and filter query (for backend)
      * 
      * @return void
      */
     public function fetchAll($paginated=false,$query=null)
     {
        if($paginated) {
             $select = ($query) ? $this->search($query) : new \Zend\Db\Sql\Select('admin_user');
             $select->join('users_roles', "admin_user.uid = users_roles.uid", array('rid'), 'inner');
             $select->join('roles', "users_roles.rid = roles.rid", array('role_name' => 'name'), 'inner');
             $select->order('admin_user.updated_at DESC');
             //$resultSetPrototype = new \Zend\Db\ResultSet\ResultSet;
             //$resultSetPrototype->setArrayObjectPrototype(new \AdminUser\Model\Dto\AdminUserDto);
             //$paginatorAdapter = new DbSelect($select,$this->tableGateway->getAdapter(),$resultSetPrototype);
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
     
    public function search($criteria)
    {
        $select = new \Zend\Db\Sql\Select('admin_user');
        
        if(!empty($criteria['username'])) {
            $select->where->like('username',"%$criteria[username]%");    
        }
        if(!empty($criteria['rid'])) {
            $select->where->in('users_roles.rid',$criteria['rid']);
        }
        if(!empty($criteria['status'])) {
            $select->where->in('status',$criteria['status']);
        }
        if(!empty($criteria['created_at_from'])) {
            $select->where->greaterThanOrEqualTo('admin_user.created_at',$criteria['created_at_from']);
        }
        if(!empty($criteria['created_at_to'])) {
            $select->where->lessThanOrEqualTo('admin_user.created_at',$criteria['created_at_to']);
        }
        
        return $select;
        
    }
    
    public function save(\AdminUser\Model\Dto\AdminUserDto $user)
    {
        $data = $user->getArrayCopy();
        $uid = (int) $user->uid;
        if($uid == 0) {
            $this->tableGateway->insert($data);
            return $this->tableGateway->lastInsertValue;
        } else {
            if($this->fetchOne($uid)) {
                $this->tableGateway->update($data,array('uid' => $uid));
            } else {
                return false;
            }
        }
    }

     public function delete($uid)
     {
          $this->tableGateway->delete(array('uid' => $uid));
     }
    
}

