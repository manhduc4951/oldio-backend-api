<?php

namespace Api\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\JsonModel;

class AuthRestController extends AbstractActionController
{
    protected $tokenDao;
    
    protected $refreshTokenDao;
    
    protected $userDao;
    
    protected $userBusiness;
    
    const ACCESS_LIFETIME = 3600;
    
    const REFRESH_TOKEN_LIFETIME = 1209600;
    
    public function getTokenDao()
    {
        if (!$this->tokenDao) {
            $sm = $this->getServiceLocator();
            $this->tokenDao = $sm->get('Api\Model\TokenDao');
        }
        return $this->tokenDao;
    }
    
    public function getRefreshTokenDao()
    {
        if (!$this->refreshTokenDao) {
            $sm = $this->getServiceLocator();
            $this->refreshTokenDao = $sm->get('Api\Model\RefreshTokenDao');
        }
        return $this->refreshTokenDao;
    }
    
    public function getUserDao()
    {
        if(!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    public function getUserBusiness()
    {
        if(!$this->userBusiness) {
            $sm = $this->getServiceLocator();
            $this->userBusiness = $sm->get('User\Business\UserBussiness');
        }
        return $this->userBusiness;
    }
    
    public function loginAction()
    {
        $type = $this->getRequest()->getPost('type');
        if($type == 'facebook') {
            return $this->loginFacebookAction();
        }
        $authenticateService = $this->getServiceLocator()->get('Api\Authenticate\AuthenticationService');
        $adapter = $this->getServiceLocator()->get('Api\Authenticate\Adapter');
        
        $result = $authenticateService
            ->setAdapter($adapter)
            ->authenticate()
        ;
        
        if ($result->isValid()) {
            $token = $this->getTokenDao()->fetchOne($result->getAccessToken());
            $token->refresh_token = $result->getRefreshToken();
            $token->expires = AuthRestController::ACCESS_LIFETIME;
            $token->refresh_token_expires = AuthRestController::REFRESH_TOKEN_LIFETIME;
            /*Check storage plan expired*/
            $user = $this->getUserDao()->fetchOneBy('id',$token->user_id);
            if(strtotime($user->storage_plan_updated_at) < time()) {
                $user->storage_plan_id = 1;
                $this->getUserDao()->save($user);
            }
            return new JsonModel($token);
            
        } else {            
            return new JsonModel(array(
                'code' => 400,
                'status' => 'error',
                'message' => $result->getMessages(),
                
            ));
        }
        die;
    }
    
    public function loginFacebookAction()
    {  
        $request = $this->getRequest();
        $facebookId = $request->getPost('username');
        $accessToken = $request->getPost('password');
        $email = $request->getPost('email');
        if(empty($facebookId) || empty($accessToken) || empty($email)) {
            return new JsonModel(array(
                'code' => 400,
                'status' => 'error',
                'message' => 'Please fill up the parameters',
            ));
        }
        
        //$user = $this->getUserDao()->fetchOneBy('facebook_id',$facebookId);
        $user = $this->getUserDao()->fetchOneBy('username',$email);
        $authenticateService = $this->getServiceLocator()->get('Api\Authenticate\AuthenticationService');
        $adapter = $this->getServiceLocator()->get('Api\Authenticate\Adapter');
        $request = $this->getRequest()->getPost();
        $request = $request->toArray();
        if($user) {
            $request['username'] = $user->username;
            $request['password'] = $user->password;
            $request = new \OAuth2_Request($_GET, $request,array(), $_COOKIE, $_FILES, $_SERVER);
            $adapter->setRequest($request);        
            $result = $authenticateService
                ->setAdapter($adapter)
                ->authenticate();
            if ($result->isValid()) {
                $token = $this->getTokenDao()->fetchOne($result->getAccessToken());
                $token->refresh_token = $result->getRefreshToken();
                $token->expires = AuthRestController::ACCESS_LIFETIME;
                $token->refresh_token_expires = AuthRestController::REFRESH_TOKEN_LIFETIME;
                $token->first_login = 0;
                /***update facebook id***/
                if(!$user->facebook_id) {
                    $user->facebook_id = $facebookId;
                    $this->getUserDao()->save($user);
                }
                /*Check storage plan expired*/
                if(strtotime($user->storage_plan_updated_at) < time()) {
                    $user->storage_plan_id = 1;
                    $this->getUserDao()->save($user);
                }
                return new JsonModel($token);
                
            } else {            
                return new JsonModel(array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => $result->getMessages(),
                    
                ));
            }
            /***create token***/
//            $token = new \Api\Model\Dto\TokenDto;
//            $token->access_token = md5(rand());
//            $token->client_id = 123456789;
//            $token->user_id = $user->id;
//            $token->expires = date('Y-m-d H:i:s', time()+3600);
//            $this->getTokenDao()->save($token);
            /*Create refresh token*/
//            $refreshToken = new \Api\Model\Dto\RefreshTokenDto;
//            $refreshToken->refresh_token = md5(rand());
//            $token->client_id = 123456789;
//            $token->user_id = $user->id;
//            $token->expires = date('Y-m-d H:i:s', time()+14*86400);
//            $this->getRefreshTokenDao()->save($refreshToken);
//            $token->refresh_token = $refreshToken->refresh_token;
            
        } else {
            /***create new user***/
            $userDto = new \User\Model\Dto\UserDto;
            $userDto->id = 0;
            $userDto->facebook_id = $facebookId;
            $userDto->username = $email;
            $userDto->password = rand();
            $newUser = $this->getUserBusiness()->createUserRest($userDto);
            /*Oauth*/
            $request['username'] = $userDto->username;
            $request['password'] = $userDto->password;
            $request = new \OAuth2_Request($_GET, $request,array(), $_COOKIE, $_FILES, $_SERVER);
            $adapter->setRequest($request);        
            $result = $authenticateService
                ->setAdapter($adapter)
                ->authenticate();
            if ($result->isValid()) {
                $token = $this->getTokenDao()->fetchOne($result->getAccessToken());
                $token->refresh_token = $result->getRefreshToken();
                $token->expires = AuthRestController::ACCESS_LIFETIME;
                $token->refresh_token_expires = AuthRestController::REFRESH_TOKEN_LIFETIME;
                $token->first_login = 1;
                return new JsonModel($token);
                
            } else {            
                return new JsonModel(array(
                    'code' => 400,
                    'status' => 'error',
                    'message' => $result->getMessages(),
                    
                ));
            }
            /***create token***/
//            $token = new \Api\Model\Dto\TokenDto;
//            $token->access_token = md5(rand());
//            $token->client_id = 123456789;
//            $token->user_id = $newUser->id;
//            $token->expires = date('Y-m-d H:i:s', time()+3600);
//            $this->getTokenDao()->save($token);
            /*Create refresh token*/
//            $refreshToken = new \Api\Model\Dto\RefreshTokenDto;
//            $refreshToken->refresh_token = md5(rand());
//            $refreshToken->client_id = 123456789;
//            $refreshToken->user_id = $user->id;
//            $refreshToken->expires = date('Y-m-d H:i:s', time()+14*86400);
//            $this->getRefreshTokenDao()->save($refreshToken);
            
//            $token->refresh_token = $refreshToken->refresh_token;
//            $token->first_login = 1;
//            return new JsonModel($token->getArrayCopy());
        }
        die;
    }
    
}