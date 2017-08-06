<?php
namespace User\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class CountryDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
    
    /**
     * Get all countries
     * 
     * @return
     */
    public function fetchAll()
    {
        $resultSet = $this->tableGateway->select();
        return $resultSet;
    }
    
}

