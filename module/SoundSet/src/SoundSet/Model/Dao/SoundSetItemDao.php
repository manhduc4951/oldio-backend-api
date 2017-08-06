<?php
namespace SoundSet\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class SoundSetItemDao
{
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
         $this->tableGateway = $tableGateway;
     }
     
     public function save(\SoundSet\Model\Dto\SoundSetItemDto $soundSetItem)
     {  
          $data = $soundSetItem->getArrayCopy();  
          $id = (int)$soundSetItem->id;
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
     
     public function deleteBy($column,$value)
     {
        $this->tableGateway->delete(array($column=>$value));
     }
     
     public function fetchAllBy($column,$value)
     {
        $select = new \Zend\Db\Sql\Select;
        $select->from('sound_set_item');
        $select->where(array($column => $value));
        $select->order('name ASC');
        
        $resultSet = $this->tableGateway->selectWith($select);
        return $resultSet;
     }
     
}

