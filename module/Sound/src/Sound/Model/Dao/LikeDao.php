<?php
namespace Sound\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class LikeDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Save like into db
     * 
     * @param mixed $like
     * @return void
     */
    public function save(\Sound\Model\Dto\LikeDto $like)
    {  
        $data = $like->getArrayCopy();        
        $this->tableGateway->insert($data);
    }
    
    /**
     * Check user have clicked like or not
     * 
     * @param mixed $sound_id
     * @param mixed $user_id
     * @return
     */
    public function fetchOne($sound_id, $user_id)
    {  
        $rowset = $this->tableGateway->select(array('sound_id' => $sound_id, 'user_id' => $user_id));
        $row = $rowset->current();
                
        return $row;
    }
    
    /**
     * Delete like(s) by specific column
     * 
     * @param mixed $column
     * @param mixed $value
     * @return void
     */
    public function deleteBy($column,$value)
    {
        $this->tableGateway->delete(array($column=>$value));
    }
    
}

