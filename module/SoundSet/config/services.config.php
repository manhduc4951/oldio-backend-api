<?php
use Zend\Db\TableGateway\TableGateway;

return array(
    'factories' => array(
        'SoundSet\Model\SoundSetDao' =>  function($sm) {
            $tableGateway = $sm->get('SoundSetTableGateway');
            $table = new \SoundSet\Model\Dao\SoundSetDao($tableGateway);
            return $table;
        },
        'SoundSetTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('sound_set', $dbAdapter, null);
        },
        'SoundSet\Model\UserSoundSetDao' =>  function($sm) {
            $tableGateway = $sm->get('UserSoundSetTableGateway');
            $table = new \SoundSet\Model\Dao\UserSoundSetDao($tableGateway);
            return $table;
        },
        'UserSoundSetTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('user_sound_set', $dbAdapter, null);
        },
        'SoundSet\Model\SoundSetItemDao' =>  function($sm) {
            $tableGateway = $sm->get('SoundSetItemTableGateway');
            $table = new \SoundSet\Model\Dao\SoundSetItemDao($tableGateway);
            return $table;
        },
        'SoundSetItemTableGateway' => function ($sm) {
            $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
            return new TableGateway('sound_set_item', $dbAdapter, null);
        },
        'SoundSet\Business\SoundSetBussiness' => function($sm) {
            $business = new \SoundSet\Business\SoundSetBusiness;
		    $business->setServiceManager($sm);
   		    return $business;
		},        
    ),
);