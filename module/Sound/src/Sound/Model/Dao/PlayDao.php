<?php
namespace Sound\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class PlayDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    public function save(\Sound\Model\Dto\PlayDto $view)
    {  
        $data = $view->getArrayCopy();        
        $this->tableGateway->insert($data);
    }
    
    public function fetchOne($soundId, $userId)
    {  
        $rowset = $this->tableGateway->select(array('sound_id' => $soundId, 'user_id' => $userId));
        $row = $rowset->current();
                
        return $row;
    }
    
    public function deleteBy($column,$value)
    {
        $this->tableGateway->delete(array($column=>$value));
    }
    
}

