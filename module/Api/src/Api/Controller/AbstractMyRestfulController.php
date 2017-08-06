<?php
namespace Api\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
//use Zend\Mvc\MvcEvent;
use Zend\Http\Request as HttpRequest;
use Zend\Json\Json;
use Zend\Mvc\Exception;
use Zend\Mvc\MvcEvent;
use Zend\Stdlib\RequestInterface as Request;
use Zend\Stdlib\ResponseInterface as Response;
use Zend\View\Model\JsonModel;

abstract class AbstractMyRestfulController extends AbstractRestfulController
{
    public function onDispatch(MvcEvent $e)
    {   
        $application = $e->getApplication();
        $sm = $application->getServiceManager();
        
        $params = $e->getRouteMatch()->getParams();
        $check = false;
        if($params['controller'] == 'User\Controller\UserRest' && $e->getRequest()->isPost() && !isset($params['sub_route'])) {
            $check = true;
        } elseif($params['controller'] == 'User\Controller\ForgotPasswordRest') {
            $check = true;
        } elseif($params['controller'] == 'Settings\Controller\PushRest') {
            $check = true;
        }
        
        $e->getRequest()->getContent();
        if ($sm->get('Api\Authenticate\AuthenticationService')->hasIdentity() == true || $check == true) {
            return parent::onDispatch($e);
        } else {
            die("cannot access my apis");
        }
    }
    
    public function formInvalid()
    {
        return new JsonModel(array(
            'code' => 404,
            'status' => 'error',
            'message' => 'Form invalid',
        ));
    }
    
    public function success($data = null)
    {
        return new JsonModel(array(
            'code' => 200,
            'status' => 'success',
            'data' => $data,
        ));
    }
    
    public function error($message = null, $code = 404)
    {
        return new JsonModel(array(
            'code' => $code,
            'status' => 'error',
            'message' => $message,
        ));
    }
    
    public function failure($message = null)
    {
        return new JsonModel(array(
            'status' => 'failure',
            'message' => $message,
        ));
    }
    
}