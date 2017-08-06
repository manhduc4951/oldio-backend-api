<?php
namespace Permission\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class UserRoleDao
{
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
          $this->tableGateway = $tableGateway;
     }

//     public function save($userRole)
//     {  
//          $data = $userRole->getArrayCopy();
//          $this->tableGateway->insert($data);
//     }
     
     public function fetchOne($uid,$rid)
     {
          $uid  = (int) $uid;
          $rid = (int) $rid;
          $rowset = $this->tableGateway->select(array('uid' => $uid,'rid' => $rid));
          $rowset->setArrayObjectPrototype(new \Permission\Model\Dto\UserRole);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }
     
//     public function save(\Permission\Model\Dto\UserRole $userRole)
//     {
//        $data = $userRole->getArrayCopy();
//        $uid = (int) $user->uid;
//        if($uid == 0) {
//            $this->tableGateway->insert($data);
//            return $this->tableGateway->lastInsertValue;
//        } else {
//            if($this->fetchOne($uid)) {
//                $this->tableGateway->update($data,array('uid' => $uid));
//            } else {
//                return false;
//            }
//        }
//     }
     
     public function add(\Permission\Model\Dto\UserRole $userRole)
     {
        $data = $userRole->getArrayCopy();
        $this->tableGateway->insert($data);
     }
     
     public function update(\Permission\Model\Dto\UserRole $userRole)
     {
        $data = $userRole->getArrayCopy();
        $this->tableGateway->update($data,array('uid' => $userRole->uid));
//        if($this->fetchOne($userRole->uid,$userRole->rid)) {
//            
//        } else {
//            return false;
//        }
     }
     
     public function delete($uid)
     {
          $this->tableGateway->delete(array('uid' => $uid));
     }

     
}
