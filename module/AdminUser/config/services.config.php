<?php
use Zend\Db\TableGateway\TableGateway;

return array(
    'factories' => array(
        'AdminUser\Model\AdminUserDao' =>  function($sm) {
            $tableGateway = $sm->get('AdminUserTableGateway');
            $table = new \AdminUser\Model\Dao\AdminUserDao($tableGateway);
            return $table;
        },
        'AdminUserTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('admin_user', $dbAdapter, null);
        },
        'AdminUser\Business\AdminUserBussiness' => function($sm) {
		    $business = new \AdminUser\Business\AdminUserBusiness;
		    $business->setServiceManager($sm);
   		    return $business;
		},           
    ),
);