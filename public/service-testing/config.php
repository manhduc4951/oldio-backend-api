<?php

$config = array(
    'login' => array(
        'url' => 'auth-rest',
        'method' => 'post',
        'params' => array('username','password','grant_type','client_id','type','email','refresh_token'),
    ),
    'facebook-find-friend' => array(
        'url' => 'facebook-rest',
        'method' => 'get',
        'params' => array('facebook_id','token'),
    ),
    'forgot-password' => array(
        'url' => 'forgot-password-rest',
        'method' => 'get',
        'params' => array('email'),
    ),
    'like-post' => array(
        'url' => 'like-rest',
        'method' => 'post',
        'params' => array('sound_id'),
    ),
    'unlike' => array(
        'url' => 'like-rest/:sound_id',
        'method' => 'delete',
        'params' => array(),
    ),
    'like-check-exist' => array(
        'url' => 'like-rest',
        'method' => 'get',
        'params' => array('user_id','sound_id'),
    ),
    'comment-post-comment' => array(
        'url' => 'comment-rest',
        'method' => 'post',
        'params' => array('sound_id','comment'),
    ),
    'comments-get-sound-comments' => array(
        'url' => 'comment-rest',
        'method' => 'get',
        'params' => array('sound_id','limit','offset','updated_at'),
    ),
    'comment-delete-comment' => array(
        'url' => 'comment-rest/:id',
        'method' => 'delete',
        'params' => array(),
    ),
    'user-register-user' => array(
        'url' => 'user-rest',
        'method' => 'post',
        'params' => array('username','password','confirm_password'),
    ),
    'user-update-profile' => array(
        'url' => 'user-rest/:id',
        'method' => 'put',
        'params' => array('display_name','full_name','phone','birthday','gender','country_id','description'),
    ),
    'user-update-facebook-id' => array(
        'url' => 'user-rest/:id/update-facebook-info',
        'method' => 'put',
        'params' => array('facebook_id'),
    ),
    'user-update-storage_plan' => array(
        'url' =>'user-rest/:id/storage-plan',
        'method' => 'put',
        'params' => array('storage_plan_id'),
    ),
    'user-change-password' => array(
        'url' => 'user-rest/:id/change-password',
        'method' => 'put',
        'params' => array('current_password','new_password','confirm_password'),
    ),
    'user-get-one' => array(
        'url' => 'user-rest/:id',
        'method' => 'get',
        'params' => array(),
    ),
    'user-avatar-upload' => array(
        'url' => 'user-rest/:id/avatar',
        'method' => 'post',
        'params' => array('avatar' => 'file'),
    ),
    'user-cover-image-upload' => array(
        'url' => 'user-rest/:id/cover_image',
        'method' => 'post',
        'params' => array('cover_image' => 'file'),
    ),
    'follow' => array(
        'url' => 'follow-rest',
        'method' => 'post',
        'params' => array('user_id_audience'),
    ),
    'follow-turn-off' => array(
        'url' => 'follow-rest/:user_id_audience',
        'method' => 'delete',
        'params' => array(),
    ),
    'follow-get-list' => array(
        'url' => 'follow-rest',
        'method' => 'get',
        'params' => array('limit','offset','updated_at'),
    ),
    'home' => array(
        'url' => 'home-rest',
        'method' => 'get',
        'params' => array('limit','offset','time_from','time_to'),
    ),
    'notification' => array(
        'url' => 'notification-rest/:user_id',
        'method' => 'get',
        'params' => array('limit','offset','time_from','time_to'),
    ),
    'notification-update' => array(
        'url' => 'notification-rest/:id',
        'method' => 'put',
        'params' => array(),
    ),
    'audience-get-list' => array(
        'url' => 'audience-rest',
        'method' => 'get',
        'params' => array('limit','offset','updated_at'),
    ),
    'audience-turn-off' => array(
        'url' => 'audience-rest/:user_id_following',
        'method' => 'delete',
        'params' => array(),
    ),
    'category-get-list' => array(
        'url' => 'category-rest',
        'method' => 'get',
        'params' => array(),
    ),
    'category-get-user-belong' => array(
        'url' => 'category-rest/:id',
        'method' => 'get',
        'params' => array('limit','offset','updated_at'),
    ),
    'sound-get-one' => array(
        'url' => 'sound-rest/:id',
        'method' => 'get',
        'params' => array(),
    ),
    'sound-get-one-sub-information' => array(
        'url' => 'sound-rest/:id/subinfo',
        'method' => 'get',
        'params' => array(),
    ),
    'sound-get-list' => array(
        'url' => 'sound-rest',
        'method' => 'get',
        'params' => array('user_id','limit','offset','updated_at'),
    ),
    'sound-upload' => array(
        'url' => 'sound-rest',
        'method' => 'post',
        'params' => array('category_id','title','thumbnail'=>'file','thumbnail2' => 'file','thumbnail3' => 'file','description','sound_path'=>'file','duration','type','connect_facebook','connect_twitter','tags'),
    ),
//    'sound-edit' => array(
//        'url' => 'sound-rest/:id',
//        'method' => 'put',
//        'params' => array('sound_path'),
//        //'params' => array('category_id','title','thumbnail'=>'file','description','sound_path','type','connect_facebook','connect_twitter','tags'),
//    ),
    'sound-edit' => array(
        'url' => 'sound-rest/:id/update',
        'method' => 'post',
        'params' => array('category_id','title','thumbnail'=>'file','thumbnail2' => 'file','thumbnail3' => 'file','description','type','connect_facebook','connect_twitter','tags'),
    ),
    'sound-delete' => array(
        'url' => 'sound-rest/:id',
        'method' => 'delete',
        'params' => array(),
    ),
//    'sound-view' => array(
//        'url' => 'sound-rest/:id/view',
//        'method' => 'put',
//        'params' => array(),
//    ),
    'sound-view' => array(
        'url' => 'view-rest',
        'method' => 'post',
        'params' => array('sound_id'),
    ),
//    'sound-play' => array(
//        'url' => 'sound-rest/:id/play',
//        'method' => 'put',
//        'params' => array(),
//    ),
    'sound-play' => array(
        'url' => 'play-rest',
        'method' => 'post',
        'params' => array('sound_id'),
    ),
    'favorite-add-to-list' => array(
        'url' => 'favorite-rest',
        'method' => 'post',
        'params' => array('sound_id'),
    ),
    'favorite-get-list' => array(
        'url' => 'favorite-rest',
        'method' => 'get',
        'params' => array('limit','offset','updated_at'),
    ),
    'favorite-delete' => array(
        'url' => 'favorite-rest/:id',
        'method' => 'delete',
        'params' => array(),
    ),
    'favorite-update-order' => array(
        'url' => 'favorite-rest',
        'method' => 'put',
        'params' => array('sound_id','order'),
    ),
    'soundset-get-list-shop' => array(
        'url' => 'soundset-rest',
        'method' => 'get',
        'params' => array('limit','offset','updated_at','price'),
    ),
    'soundset-get-one-shop' => array(
        'url' => 'soundset-rest/:id',
        'method' => 'get',
        'params' => array(),
    ),
    'search-user' => array(
        'url' => 'search/user',
        'method' => 'get',
        'params' => array('filter'),
    ),
    'search-sound' => array(
        'url' => 'search/sound',
        'method' => 'get',
        'params' => array('filter'),
    ),
    'search-tag' => array(
        'url' => 'search/tag',
        'method' => 'get',
        'params' => array('filter'),
    ),
    'search-sound-tag' => array(
        'url' => 'search/sound-tag',
        'method' => 'get',
        'params' => array('filter'),
    ),
    'search-all' => array(
        'url' => 'search/all',
        'method' => 'get',
        'params' => array('filter'),
    ),
    'sound-board-get-list' => array(
        'url' => 'user-soundset-rest',
        'method' => 'get',
        'params' => array(),
    ),
//    'soundset-free' => array(
//        'url' => 'soundset-free-rest',
//        'method' => 'get',
//        'params' => array(),
//    ),
    'sound-board-delete' => array(
        'url' => 'user-soundset-rest/:id',
        'method' => 'delete',
        'params' => array(),
    ),
    'sound-set-purchase' => array(
        'url' => 'user-soundset-rest',
        'method' => 'post',
        'params' => array('sound_set_id'),
    ),
    'sound-board-update-order' => array(
        'url' => 'user-soundset-rest',
        'method' => 'put',
        'params' => array('sound_set_id','order'),
    ),
    'sound-set-active-inactive' => array(
        'url' => 'user-soundset-rest/:sound_set_id',
        'method' => 'put',
        'params' => array(),
    ),
    'about' => array(
        'url' => 'appconfig-rest',
        'method' => 'get',
        'params' => array(),
    ),
    'storage-plan-get-list' => array(
        'url' => 'storage-plan-rest',
        'method' => 'get',
        'params' => array(),
    ),
//    'test-push-2' => array(
//        'url' => 'settings-rest',
//        'method' => 'post',
//        'params' => array(),
//    ),
    'device-token-insert' => array(
        'url' => 'device-token-rest',
        'method' => 'post',
        'params' => array('device_token'),
    ),
    'device-token-delete' => array(
        'url' => 'device-token-rest/:device_token',
        'method' => 'delete',
        'params' => array(),
    ),
    'settings-get-settings' => array(
        'url' => 'settings-rest',
        'method' => 'get',
        'params' => array(),
    ),
    'settings-update-connection' => array(
        'url' => 'settings-rest/connection',
        'method' => 'put',
        'params' => array('connect_facebook','connect_twitter'),
    ),
    'settings-update-sound-quality' => array(
        'url' => 'settings-rest/sound-quality',
        'method' => 'put',
        'params' => array('sound_quality'),
    ),
    'settings-update-notification' => array(
        'url' => 'settings-rest/notification',
        'method' => 'put',
        //'params' => array('email_follow_me','email_comments_on_my_post','email_comments_on_a_post_i_care','email_like_my_sound','push_follow_me','push_comments_on_my_post','push_comments_on_a_post_i_care','push_like_my_sound'),
        'params' => array('notification'),
    ),
    'testing-not-user' => array(
        'url' => 'appconfig-rest/:id',
        'method' => 'get',
        'params' => array(),
    ),
);