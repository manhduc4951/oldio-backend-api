<?php
namespace Settings\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class AppConfigController extends AbstractActionController
{
    protected $appConfigDao;
    
    public function getAppConfigDao()
    {
        if(!$this->appConfigDao) {
            $sm = $this->getServiceLocator();
            $this->appConfigDao = $sm->get('Settings\Model\AppConfigDao');
        }
        return $this->appConfigDao;
    }
    
    public function indexAction()
    {
        $appConfigs = $this->getAppConfigDao()->fetchAll();
        
        /*Flash message*/
        $messages = '';
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
               $messages = $flashMessenger->getMessages();
        }
        
        return array(
            'appConfigs' => $appConfigs,
            'messages' => $messages,
            'permissions' => $this->permissionActions(array('add','edit','delete'),'settings_appconfig'),
        );
    }
    
    public function addAction()
    {
        $form = new \Settings\Form\AppConfigForm;
        
        $request = $this->getRequest();
        if($request->isPost()) {
            $formFilter = new \Settings\Form\AppConfigFormFilter;
            $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()) {
                $appConfig = new \Settings\Model\Dto\AppConfigDto;
                $appConfig->exchangeArray($form->getData());
                $this->getAppConfigDao()->save($appConfig);
                $this->flashMessenger()->addMessage('Create config successfully');
			    return $this->redirect()->toRoute('app-config');
            }
        }
        
        return array(
            'form' => $form,
        );
    }
    
    public function editAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('app-config',array('action' => 'add'));
        }
        $appConfig = $this->getAppConfigDao()->fetchOne($id);
        if(!$appConfig) {
            return $this->redirect()->toRoute('app-config');
        }
        $form = new \Settings\Form\AppConfigForm;
        $form->bind($appConfig);
        $request = $this->getRequest();
        if($request->isPost()) {
            $formFilter = new \Settings\Form\AppConfigFormFilter;
            $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $nameExclude = $formFilter->getInputFilter()->get('name')->getValidatorChain()->getValidators();
            $nameExclude[0]['instance']->setExclude(array('field'=>'id','value'=>$id));
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()) {
                $this->getAppConfigDao()->save($appConfig);
                $this->flashMessenger()->addMessage('Edit config successfully');
			    return $this->redirect()->toRoute('app-config');
                
            }
        }
        
        return array(
            'form' => $form,
            'id' => $id,
        );
    }
    
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('app-config');
        }
        $appConfig = $this->getAppConfigDao()->fetchOne($id);
        if(!$appConfig) {
            return $this->redirect()->toRoute('app-config');
        }
        $this->getAppConfigDao()->delete($id);
        return $this->redirect()->toRoute('app-config');
    }
    
}