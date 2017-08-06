<?php
namespace User\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class ForgotPasswordRestController extends AbstractMyRestfulController
{
    protected $forgotPasswordTokenDao;
    
    protected $userDao;
    
    protected $settingsBusiness;
    
    public function getForgotPasswordTokenDao()
    {
        if(!$this->forgotPasswordTokenDao) {
            $sm = $this->getServiceLocator();
            $this->forgotPasswordTokenDao = $sm->get('User\Model\ForgotPasswordTokenDao');
        }
        return $this->forgotPasswordTokenDao;
    }
    
    public function getUserDao()
    {
        if (!$this->userDao) {
            $sm = $this->getServiceLocator();
            $this->userDao = $sm->get('User\Model\UserDao');
        }
        return $this->userDao;
    }
    
    public function getSettingsBusiness()
    {
        if(!$this->settingsBusiness) {
            $sm = $this->getServiceLocator();
            $this->settingsBusiness = $sm->get('Settings\Business\SettingsBussiness');
        }
        return $this->settingsBusiness;
    }

    /**
     * Receive request from app and user and analytic
     * 
     * @return
     */
    public function getList()
    {  
        $email = $this->params()->fromQuery('email',null);
        $token = $this->params()->fromQuery('token',null);
        if(!empty($email) && !empty($token)) {
            return $this->forgotPassword(array('email' => $email,'token' => $token));
        } elseif(!empty($email)) {
            $user = $this->getUserDao()->fetchOneBy('username',$email);
            if(!$user) {
                return $this->error('Email incorrect, please check again');
            } elseif($user && $user->facebook_id) {
                return $this->error('This email belongs to a facebook account so you can get new password with the email');
            }
            return $this->sendEmailConfirm(array('email' => $email));    
        } else {
            return $this->error('Parameters failure!!!');    
        }
    }
    
    /**
     * When user accept and send confirm, go here and reset password and send new password
     * 
     * @param mixed $data
     * @return
     */
    public function forgotPassword($data)
    {
        $forgotPasswordToken = $this->getForgotPasswordTokenDao()->fetchOne($data['email'],$data['token']);
        if(!$forgotPasswordToken) {
            die('Unable to reset your password, please check the link again');
        }
        
        $user = $this->getUserDao()->fetchOneBy('username',$forgotPasswordToken->email);
        if(!$user) {
            die('Unable to reset your password, please check the link again');
        }
        
        $newPassword = rand();
        $user->password = md5($newPassword);
        $this->getUserDao()->save($user);

        $this->getSettingsBusiness()->sendEmail(array(
            'email' => $data['email'],
            'email_subject' => 'Your OnlineDio account password has been reset',
            'email_body' => "A request to change your password has been made on OnlineDio app.".
                            "<span style='display:block;'>Here's your new password.</span>".
                            "<span style='display:block;'>$newPassword</span>"
                            ,
        ));
        
        die('Reset password successfully! Please check your email.');
        
    }
    
    /**
     * Send email confirm 
     * 
     * @param mixed $data
     * @return
     */
    public function sendEmailConfirm($data)
    {
        $token = md5(rand());
        $url = $this->url()->fromRoute('forgot-password-rest',array(),array('force_canonical' => true))
               .'?email='.$data['email'].'&token='.$token;
//        $emailBody = "Hi OnlineDio user,\n\n".
//                     "We have requested to reset your forgot password from OnlineDio iOS app.\n".
//                     $url
//                     ;
        
        $emailBody = "<span style='display: block;margin-bottom:20px;'>We have requested to reset your forgot password from OnlineDio IOS app.</span>".
                     "<a style='color: #ffe000;text-decoration:none;display:block;' href='".$url."'>".$url."</a>"  
                     ;
        
        $forgotPasswordToken = new \User\Model\Dto\ForgotPasswordTokenDto;
        $forgotPasswordToken->email = $data['email'];
        $forgotPasswordToken->token = $token;
        $this->getForgotPasswordTokenDao()->save($forgotPasswordToken);
        
        $this->getSettingsBusiness()->sendEmail(array(
            'email' => $data['email'],
            'email_subject' => 'OnlineDio reset password confirmation',
            'email_body' => $emailBody,
        ));
        
        return $this->success();
    }
    
    public function create($data) {}
    
    public function delete($id) {}
    
    public function update($id, $data) {}
    
    public function get($id) {}

}
