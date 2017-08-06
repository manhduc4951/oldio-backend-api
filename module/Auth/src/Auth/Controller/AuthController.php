<?php

namespace Auth\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AuthController extends AbstractActionController {
    
    protected $adminUserDao;
    
    public function getAdminUserDao()
    {
        if (!$this->adminUserDao) {
            $sm = $this->getServiceLocator();
            $this->adminUserDao = $sm->get('AdminUser\Model\AdminUserDao');
        }
        return $this->adminUserDao;
    }

    public function loginAction() {
        
        $this->layout('layout/login.phtml');
        
        if ($this->getServiceLocator()->get('user_authenticate_service')->hasIdentity()) {
            return $this->redirect()->toRoute('home');
        }

        $flashMessenger = $this->flashMessenger();
        $loginForm = new \Auth\Form\LoginForm();
        $loginFormFilter = new \Auth\Form\LoginFormFilter();
        $loginForm->setInputFilter($loginFormFilter->getInputFilter());

        $request = $this->getRequest();

        if ($request->isPost()) {
            $post = $request->getPost();
            $loginForm->setData($post);

            if ($loginForm->isValid()) {
                return $this->forward()->dispatch('Auth', array('action' => 'authenticate'));
            }
        }

        $messages = '';
        if ($flashMessenger->hasMessages()) {
            $messages = $flashMessenger->getMessages();
        }

        return array('loginForm' => $loginForm, 'messages' => $messages);
    }

    /**
     * General-purpose authentication action
     */
    public function authenticateAction() {
        $request = $this->getRequest();
        $authService = $this->getServiceLocator()->get('user_authenticate_service');
        $authService->getAdapter()->setIdentity($request->getPost('username'))
                ->setCredential($request->getPost('password'));

        $result = $authService->authenticate();
        if ($result->isValid()) {
            $i = $this->getServiceLocator()->get('Zend\Authentication\Adapter\DbTable')->getResultRowObject();
            $userInfo = $this->getAdminUserDao()->getUserInfo($i->uid);
            $authService->getStorage()->write($userInfo);
            return $this->redirect()->toRoute('home');
        }

        $this->flashMessenger()->addMessage('Login failure.');
        return $this->redirect()->toRoute('auth/login');
    }

    public function logoutAction() {
        $this->getServiceLocator()->get('user_authenticate_service')->clearIdentity();

        return $this->redirect()->toRoute('auth/login');
    }

}