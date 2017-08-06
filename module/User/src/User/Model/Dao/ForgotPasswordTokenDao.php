<?php
namespace User\Model\Dao;

use Zend\Db\TableGateway\TableGateway;

class ForgotPasswordTokenDao
{
    protected $tableGateway;

    public function __construct(TableGateway $tableGateway)
    {
        $this->tableGateway = $tableGateway;
    }
     
     public function fetchOne($email,$token)
     {   
          $rowset = $this->tableGateway->select(array('email' => $email,'token' => $token));
          $row = $rowset->current();
          if (!$row) {
               return false;
          }
          return $row;
     }
     
     public function save(\User\Model\Dto\ForgotPasswordTokenDto $forgotPasswordTokenDto)
     {  
          $data = $forgotPasswordTokenDto->getArrayCopy();
          $this->tableGateway->insert($data);
           
     }
}

