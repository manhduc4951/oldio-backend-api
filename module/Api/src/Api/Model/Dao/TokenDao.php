<?php
namespace Api\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class TokenDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }

     public function fetchOne($token)
     {   
          $rowset = $this->tableGateway->select(array('access_token' => $token));
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }
     
     public function save(\Api\Model\Dto\TokenDto $token)
     {
        $data = $token->getArrayCopy();
        $this->tableGateway->insert($data);
     }
     
}

