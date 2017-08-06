<?php
namespace Settings\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class SettingsDao
{
     protected $tableGateway;

     public function __construct(TableGateway $tableGateway)
     {
          $this->tableGateway = $tableGateway;
     }
     
     /**
      * Get settings of an user
      * 
      * @param mixed $column
      * @param mixed $id
      * @return
      */
     public function fetchOneBy($column,$id)
     {
          $id  = (int) $id;
          $rowset = $this->tableGateway->select(array($column => $id));
          $rowset->setArrayObjectPrototype(new \Settings\Model\Dto\SettingsDto);
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }

     public function save(\Settings\Model\Dto\SettingsDto $settings)
     {  
          $data = $settings->getArrayCopy();  
          $id = (int)$settings->id;
          if ($id == 0) {
               $this->tableGateway->insert($data);
          } else {
               if ($this->fetchOneBy('id',$id)) {
                    $this->tableGateway->update($data, array('id' => $id));
               } else {
                    throw new \Exception('Form id does not exist');
               }
          }
     }

     public function delete($id)
     {
          $this->tableGateway->delete(array('id' => $id));
     }
     
}

