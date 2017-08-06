<?php
use Zend\Db\TableGateway\TableGateway;

return array(
    'factories' => array(
        'User\Model\UserDao' =>  function($sm) {
            $tableGateway = $sm->get('UserTableGateway');
            $table = new \User\Model\Dao\UserDao($tableGateway);
            return $table;
        },
        'UserTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('user', $dbAdapter, null);
        },
        'User\Model\CountryDao' =>  function($sm) {
            $tableGateway = $sm->get('CountryTableGateway');
            $table = new \User\Model\Dao\CountryDao($tableGateway);
            return $table;
        },
        'CountryTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('country', $dbAdapter, null);
        },
        'User\Model\FollowAudienceDao' =>  function($sm) {
            $tableGateway = $sm->get('FollowAudienceTableGateway');
            $table = new \User\Model\Dao\FollowAudienceDao($tableGateway);
            return $table;
        },
        'FollowAudienceTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('following_audience', $dbAdapter, null);
        },
        'User\Model\CategoryDao' =>  function($sm) {
            $tableGateway = $sm->get('CategoryTableGateway');
            $table = new \User\Model\Dao\CategoryDao($tableGateway);
            return $table;
        },
        'CategoryTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('category', $dbAdapter, null);
        },
        'User\Model\SoundCategoryDao' =>  function($sm) {
            $tableGateway = $sm->get('SoundCategoryTableGateway');
            $table = new \User\Model\Dao\SoundCategoryDao($tableGateway);
            return $table;
        },
        'SoundCategoryTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('sound_category', $dbAdapter, null);
        },
        'User\Model\ForgotPasswordTokenDao' =>  function($sm) {
            $tableGateway = $sm->get('ForgotPasswordTokenTableGateway');
            $table = new \User\Model\Dao\ForgotPasswordTokenDao($tableGateway);
            return $table;
        },
        'ForgotPasswordTokenTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('forgot_password_token', $dbAdapter, null);
        },
        'User\Business\UserBussiness' => function($sm) {
		    $business = new \User\Business\UserBusiness;
		    $business->setServiceManager($sm);
   		    return $business;
		},             
    ),
);