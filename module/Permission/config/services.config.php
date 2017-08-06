<?php
use Zend\Db\TableGateway\TableGateway;
use Zend\Db\Sql\Sql;
use Permission\Model\Dao\UserRoleDao;
use Permission\Model\Dto\UserRole;

return array (
	'factories' => array (
        'Permission\Model\PermissionDao' => function ($sm) {
        	$tableGateway = $sm->get('PermissionTableGateway');
        	$table = new \Permission\Model\Dao\PermissionDao($tableGateway);
        	return $table;
        },
        'PermissionTableGateway' => function ($sm) {
        	$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
        	return new TableGateway('permissions', $dbAdapter, null);
        },
        'Permission\Model\Dao\RoleDao' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            $table = new \Permission\Model\Dao\RoleDao($dbAdapter);
            return $table;
        },
        'Permission\Model\Business\Permission' => function($sm) {
            $cacheAdapter = $sm->get('Zend\Cache\Storage\ZendServerShm');
            
            $permissionBusiness = new \Permission\Model\Business\PermissionBusiness();
            $permissionBusiness->setServiceLocator($sm);
            $permissionBusiness->setCache($cacheAdapter);
            
            return $permissionBusiness;
        },
        'Permission\Model\Dao\UserRoleDao' =>  function($sm) {
            $tableGateway = $sm->get('UserRoleTableGateway');
            $table = new UserRoleDao($tableGateway);
            return $table;
        },
        'UserRoleTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('users_roles', $dbAdapter, null);
        },
        'Permission\Model\RoleNameDao' =>  function($sm) {
            $tableGateway = $sm->get('RoleNameTableGateway');
            $table = new \Permission\Model\Dao\RoleNameDao($tableGateway);
            return $table;
        },
        'RoleNameTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('roles', $dbAdapter, null);
        },
    ),
);