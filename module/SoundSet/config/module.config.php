<?php
return array(
    'controllers' => array(
        'invokables' => array(            
            'SoundSet\Controller\SoundSetRest' => 'SoundSet\Controller\SoundSetRestController',
            'SoundSet\Controller\UserSoundSetRest' => 'SoundSet\Controller\UserSoundSetRestController',
            'SoundSet\Controller\SoundSet' => 'SoundSet\Controller\SoundSetController',
            'SoundSet\Controller\UserSoundSet' => 'SoundSet\Controller\UserSoundSetController',
        ),
    ),         
    
    'router' => array(
        'routes' => array(
            'soundset-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/soundset-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'SoundSet\Controller\SoundSetRest',
                    ),
                 ),     
             ),
             'user-soundset-rest' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/user-soundset-rest[/:id]',
                    'constraints' => array('id' => '[0-9]+', ),
                    'defaults' => array(
                        'controller' => 'SoundSet\Controller\UserSoundSetRest',
                    ),
                 ),     
             ),
//             'soundset-free-rest' => array(
//                'type' => 'segment',
//                'options' => array(
//                    'route' => '/soundset-free-rest',
//                    'defaults' => array(
//                        'controller' => 'SoundSet\Controller\UserSoundSetRest',
//                        'action' => 'getSoundSetFree',
//                    ),
//                 ),     
//             ),
             'soundset_image' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/sound_set/image/:file',
                ),
             ),
             'soundset_zip_file' => array(
                'type'    => 'segment',
                'options' => array(
                    'route'    => '/sound_set/zip_file/:file',
                ),
             ),
             'soundset_item' => array(
                'type' => 'segment',
                'options' => array(
                    'route' => '/sound_set_item/:file',
                ),
             ),
             'sound-set' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/sound-set[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'SoundSet\Controller\SoundSet',
                        'action'     => 'index',
                    ),
                ),
             ),
             'user-sound-set' => array(
                'type'    => 'segment',
                'options' => array(
                    'route' => '/user-sound-set[/:action][/:id]',
                    'constraints' => array(
                        'action' => '[a-zA-Z][a-zA-Z0-9_-]*',
                        'id'     => '[0-9]+',
                    ),
                    'defaults' => array(
                        'controller' => 'SoundSet\Controller\UserSoundSet',
                        'action'     => 'index',
                    ),
                ),
             ),     
         ),
     ),
    'view_manager' => array(
        'strategies' => array(
            'ViewJsonStrategy',
        ),
        'template_path_stack' => array(
            'sound-set' => __DIR__ . '/../view',
        ),
    ),
);    