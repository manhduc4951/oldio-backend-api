<?php

return array(
    'navigation' => array(
        'default' => array(
            array(
                'label' => 'Home',
                'route' => 'home',
                'resource' => 'application_index_index'
            ),
            array(
                'label' => 'Admin User',
                'route' => 'admin-user',
                'resource'   => 'adminuser_adminuser_index'
//                'pages' => array(
//                    array(
//                        'label' => 'Add',
//                        'route' => 'album',
//                        'action' => 'add',
//                    ),
//                    array(
//                        'label' => 'Edit',
//                        'route' => 'album',
//                        'action' => 'edit',
//                    ),
//                    array(
//                        'label' => 'Delete',
//                        'route' => 'album',
//                        'action' => 'delete',
//                    ),
//                ),
            ),
            array(
                'label' => 'User',
                'route' => 'user',
                'resource' => 'user_user_index',
            ),
            array(
                'label' => 'Sound',
                'route' => 'sound',
                'resource' => 'sound_sound_index',
            ),
            array(
                'label' => 'Comment',
                'route' => 'comment',
                'resource' => 'sound_comment_index',
            ),
            array(
                'label' => 'SoundSet',
                'route' => 'sound-set',
                'resource' => 'soundset_soundset_index',
            ),
            array(
                'label' => 'App config',
                'route' => 'app-config',
                'resource' => 'settings_appconfig_index',
            ),
            array(
                'label' => 'Purchase',
                'route' => 'user-sound-set',
                'resource' => 'soundset_usersoundset_index',
            ),
            array(
                'label' => 'Help center',
                'route' => 'help-center',
                'resource' => 'settings_helpcenter_index',
            ),
            array(
                'label' => 'Permission',
                'route' => 'permission',
                'resource' => 'permission_index_index'
            ),
//            array(
//                'label' => 'Logout',
//                'route' => 'auth/logout',
//                'resource' => 'auth_auth_logout',
//            ),            
        ),
    ),
);