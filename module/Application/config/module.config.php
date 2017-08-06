<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2013 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

return array(
    'router' => array(
        'routes' => array(
            'home' => array(
                'type' => 'Zend\Mvc\Router\Http\Literal',
                'options' => array(
                    'route'    => '/',
                    'defaults' => array(
                        'controller' => 'Application\Controller\Index',
                        'action'     => 'index',
                    ),
                ),
            ),
            // The following is a route to simplify getting started creating
            // new controllers and actions without needing to create a new
            // module. Simply drop new controllers in, and you can access them
            // using the path /application/:controller/:action
            'application' => array(
                'type'    => 'Literal',
                'options' => array(
                    'route'    => '/application',
                    'defaults' => array(
                        '__NAMESPACE__' => 'Application\Controller',
                        'controller'    => 'Index',
                        'action'        => 'index',
                    ),
                ),
                'may_terminate' => true,
                'child_routes' => array(
                    'default' => array(
                        'type'    => 'Segment',
                        'options' => array(
                            'route'    => '/[:controller[/:action]]',
                            'constraints' => array(
                                'controller' => '[a-zA-Z][a-zA-Z0-9_-]*',
                                'action'     => '[a-zA-Z][a-zA-Z0-9_-]*',
                            ),
                            'defaults' => array(
                            ),
                        ),
                    ),
                ),
            ),
        ),
    ),
    'service_manager' => array(
        'abstract_factories' => array(
            'Zend\Cache\Service\StorageCacheAbstractServiceFactory',
            'Zend\Log\LoggerAbstractServiceFactory',
        ),
        'aliases' => array(
            'translator' => 'MvcTranslator',
        ),
        'factories' => array(
            'navigation' => 'Zend\Navigation\Service\DefaultNavigationFactory',
        ),
    ),
    'translator' => array(
        'locale' => 'en_US',
        'translation_file_patterns' => array(
            array(
                'type'     => 'gettext',
                'base_dir' => __DIR__ . '/../language',
                'pattern'  => '%s.mo',
            ),
        ),
    ),
    'controllers' => array(
        'invokables' => array(
            'Application\Controller\Index' => 'Application\Controller\IndexController'
        ),
    ),
    'controller_plugins' => array(
        'invokables' => array(
            'DeleteFiles' => 'Application\Controller\Plugin\DeleteFiles',
        )
    ),
    'view_manager' => array(
        'display_not_found_reason' => true,
        'display_exceptions'       => true,
        'doctype'                  => 'HTML5',
        'not_found_template'       => 'error/404',
        'exception_template'       => 'error/index',
        'template_map' => array(
            'layout/layout'           => __DIR__ . '/../view/layout/layout.phtml',
            'application/index/index' => __DIR__ . '/../view/application/index/index.phtml',
            'error/404'               => __DIR__ . '/../view/error/404.phtml',
            'error/index'             => __DIR__ . '/../view/error/index.phtml',
        ),
        'template_path_stack' => array(
            __DIR__ . '/../view',
        ),
    ),
    'config_ica467' => array(
        //'user_avatar_path_upload' => 'public/user/avatar_image',
        'user_avatar_path_upload' => 'user/avatar_image',
        
        //'user_cover_path_upload' => 'public/user/cover_image',
        'user_cover_path_upload' => 'user/cover_image',
        
        //'sound_thumbnail_path_upload' => 'public/sound/thumbnail',
        'sound_thumbnail_path_upload' => 'sound/thumbnail',
        'sound_thumbnail2_path_upload' => 'sound/thumbnail2',
        'sound_thumbnail3_path_upload' => 'sound/thumbnail3',
        
        //'sound_file_path_upload' => 'public/sound/sound_file',
        'sound_file_path_upload' => 'sound/sound_file',
        
        'sound_set_zip_upload_local' => 'public/sound_set/zip_file',
        'sound_set_zip_upload_s3' => 'sound_set/zip_file',
        
        'sound_set_image_upload_local' => 'public/sound_set/image',
        'sound_set_image_upload_s3' => 'sound_set/image',
        
        'sound_set_item_upload_local' => 'public/sound_set_item',
        'sound_set_item_upload_s3' => 'sound_set_item',
        
        'sound_set_item_tmp_upload_local' => 'public/sound_set_item/tmp',
        
        'category_icon' => 'category/icon',
        'category_image' => 'category/image',
        'storage_plan_image' => 'storage_plan/image',
        'image' => 'image',
        
        'certificate_apns' => 'public/ck.pem',
        'default_bucket' => 'ica467develop',
        'item_per_page' => 20,
        'email_send_forgot_password' => 'emailtesticaproject@gmail.com',
        'password_email_send_forgot_password' => '123456asas',
        'image_type' => array('jpg','gif','png'),
        'sound_type' => array('mp3','caf'),
    ),
);
