<?php
namespace Api\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class RefreshTokenDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
     
     public function save(\Api\Model\Dto\RefreshTokenDto $token)
     {
        $data = $token->getArrayCopy();
        $this->tableGateway->insert($data);
     }
     
}

