<?php
use Zend\Db\TableGateway\TableGateway;

return array(
    'factories' => array(
        'Settings\Model\AppConfigDao' =>  function($sm) {
            $tableGateway = $sm->get('AppConfigTableGateway');
            $table = new \Settings\Model\Dao\AppConfigDao($tableGateway);
            return $table;
        },
        'AppConfigTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('app_config', $dbAdapter, null);
        },
        'Settings\Model\HelpCenterDao' =>  function($sm) {
            $tableGateway = $sm->get('HelpCenterTableGateway');
            $table = new \Settings\Model\Dao\HelpCenterDao($tableGateway);
            return $table;
        },
        'HelpCenterTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('help_center', $dbAdapter, null);
        },
        'Settings\Model\NotificationDao' =>  function($sm) {
            $tableGateway = $sm->get('NotificationTableGateway');
            $table = new \Settings\Model\Dao\NotificationDao($tableGateway);
            return $table;
        },
        'NotificationTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('notification', $dbAdapter, null);
        },
        'Settings\Model\SettingsDao' =>  function($sm) {
            $tableGateway = $sm->get('SettingsTableGateway');
            $table = new \Settings\Model\Dao\SettingsDao($tableGateway);
            return $table;
        },
        'SettingsTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('settings', $dbAdapter, null);
        },
        'Settings\Model\DeviceTokenDao' =>  function($sm) {
            $tableGateway = $sm->get('DeviceTokenTableGateway');
            $table = new \Settings\Model\Dao\DeviceTokenDao($tableGateway);
            return $table;
        },
        'DeviceTokenTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('device_token', $dbAdapter, null);
        },
        'Settings\Model\StoragePlanDao' =>  function($sm) {
            $tableGateway = $sm->get('StoragePlanTableGateway');
            $table = new \Settings\Model\Dao\StoragePlanDao($tableGateway);
            return $table;
        },
        'StoragePlanTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('storage_plans', $dbAdapter, null);
        },
        'Settings\Business\SettingsBussiness' => function($sm) {
            $business = new \Settings\Business\SettingsBusiness;
		    $business->setServiceManager($sm);
   		    return $business;
		},
    ),
);