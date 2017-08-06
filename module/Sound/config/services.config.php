<?php

use Zend\Db\TableGateway\TableGateway;

return array(
    'factories' => array(
        'Sound\Model\CommentDao' =>  function($sm) {
            $tableGateway = $sm->get('CommentTableGateway');
            $table = new \Sound\Model\Dao\CommentDao($tableGateway);
            return $table;
        },
        'CommentTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('comment', $dbAdapter, null);
        },
        'Sound\Model\SoundDao' =>  function($sm) {
            $tableGateway = $sm->get('SoundTableGateway');
            $table = new \Sound\Model\Dao\SoundDao($tableGateway);
            return $table;
        },
        'SoundTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('sound', $dbAdapter, null);
        },                 
        'Sound\Model\LikeDao' =>  function($sm) {
            $tableGateway = $sm->get('LikeTableGateway');
            $table = new \Sound\Model\Dao\LikeDao($tableGateway);
            return $table;
        },
        'LikeTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('like', $dbAdapter, null);
        },
        'Sound\Model\ViewDao' =>  function($sm) {
            $tableGateway = $sm->get('ViewTableGateway');
            $table = new \Sound\Model\Dao\ViewDao($tableGateway);
            return $table;
        },
        'ViewTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('view', $dbAdapter, null);
        },
        'Sound\Model\PlayDao' =>  function($sm) {
            $tableGateway = $sm->get('PlayTableGateway');
            $table = new \Sound\Model\Dao\PlayDao($tableGateway);
            return $table;
        },
        'PlayTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('play', $dbAdapter, null);
        },           
        'Sound\Model\FavoriteDao' =>  function($sm) {
            $tableGateway = $sm->get('FavoriteTableGateway');
            $table = new \Sound\Model\Dao\FavoriteDao($tableGateway);
            return $table;
        },
        'FavoriteTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('favorite', $dbAdapter, null);
        },        
        'Sound\Business\SoundBussiness' => function($sm) {
		    $business = new \Sound\Business\SoundBusiness;
		    $business->setServiceManager($sm);
   		    return $business;
		},                                     
    ),
);