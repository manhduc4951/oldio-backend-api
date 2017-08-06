<?php
namespace Settings\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class NotificationDao
{
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
          $this->tableGateway = $tableGateway;
     }

     public function save(\Settings\Model\Dto\NotificationDto $notification)
     {  
          $data = $notification->getArrayCopy();
          $this->tableGateway->insert($data);
     }
     
     public function update(\Settings\Model\Dto\NotificationDto $notification)
     {
          $data = $notification->getArrayCopy();
          $id = (int)$notification->id;
          if ($this->fetchOne($id)) {
               $this->tableGateway->update($data, array('id' => $id));
          }
     }
     
     /**
      * Set all notifications of an user to read
      * 
      * @return void
      */
     public function setAllToRead($userId)
     {
        $this->tableGateway->update(array('read' => \Settings\Model\Dto\NotificationDto::NOTIFICATION_READ), array('my_user_id' => $userId,'read' => \Settings\Model\Dto\NotificationDto::NOTIFICATION_UNREAD));
     }
     
     public function fetchOne($id)
     {
          $id  = (int) $id;
          $rowset = $this->tableGateway->select(array('id' => $id));
          $rowset->setArrayObjectPrototype(new \Settings\Model\Dto\NotificationDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }
     
     /**
      * Get all notifications of an user
      * 
      * @param mixed $criteria
      * @return
      */
     public function fetchAll(array $criteria = array())
     {
        $limit = (isset($criteria['limit'])) ? $criteria['limit'] : null;
        $offset = (isset($criteria['offset'])) ? $criteria['offset'] : null;
        $timeFrom = (isset($criteria['time_from'])) ? $criteria['time_from'] : null;
        $timeTo = (isset($criteria['time_to'])) ? $criteria['time_to'] : null;
        $myUserId = (isset($criteria['my_user_id'])) ? $criteria['my_user_id'] : null;
          
        $select = new \Zend\Db\Sql\Select;
        $select->from('notification');
        $select->where(array('notification.my_user_id' => $myUserId));
        $select->join('user','user.id = notification.user_id',array('avatar'),'inner');
        if(strlen($timeFrom)) {
          $select->where->greaterThanOrEqualTo('notification.created_at',$timeFrom);
        }
        if(strlen($timeTo)) {
          $select->where->lessThanOrEqualTo('notification.created_at',$timeTo);
        }
        if(strlen($limit)) {
            $select->limit((int)$limit);    
        }
        if(strlen($limit) && strlen($offset)) {
            $select->limit((int)$limit); 
            $select->offset((int)$offset);    
        }
        $select->order('notification.created_at DESC');
        $select->order('notification.id DESC');
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
     }
     
     /**
      * Get all unread notifications of an user
      * 
      * @return
      */
     public function fetchAllUnreadNotification($userId)
     {
        $select = new \Zend\Db\Sql\Select;
        $select->from('notification');
        $select->where(array('my_user_id' => $userId, 'read' => 0));
        $resultSet = $this->tableGateway->selectWith($select);
        
        return $resultSet;
     }
     
}

