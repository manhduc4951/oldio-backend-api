<?php
namespace Settings\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class HelpCenterController extends AbstractActionController
{
    protected $helpCenterDao;
    
    public function getHelpCenterDao()
    {
        if(!$this->helpCenterDao) {
            $sm = $this->getServiceLocator();
            $this->helpCenterDao = $sm->get('Settings\Model\HelpCenterDao');
        }
        return $this->helpCenterDao;
    }
    
    public function indexAction()
    {  
        $helpCenters = $this->getHelpCenterDao()->fetchAll();
        
        /*Flash message*/
        $messages = '';
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
               $messages = $flashMessenger->getMessages();
        }
        
        return array(
            'helpCenters' => $helpCenters,
            'messages' => $messages,
            'permissions' => $this->permissionActions(array('add','edit','delete'),'settings_helpcenter'),
        );
    }
    
    public function addAction()
    {  
        $form = new \Settings\Form\HelpCenterForm;
        
        $request = $this->getRequest();
        if($request->isPost()) {
            $formFilter = new \Settings\Form\HelpCenterFormFilter;
            $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()) {
                $helpCenter = new \Settings\Model\Dto\HelpCenterDto;
                $helpCenter->exchangeArray($form->getData());
                $this->getHelpCenterDao()->save($helpCenter);
                $this->flashMessenger()->addMessage('Create Help center successfully');
			    return $this->redirect()->toRoute('help-center');
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
            return $this->redirect()->toRoute('help-center',array('action' => 'add'));
        }        
        $helpCenter = $this->getHelpCenterDao()->fetchOne($id);
        if(!$helpCenter) {
            return $this->redirect()->toRoute('help-center');
        }
        $form = new \Settings\Form\HelpCenterForm;
        $form->bind($helpCenter);
        $request = $this->getRequest();
        if($request->isPost()) {
            $formFilter = new \Settings\Form\HelpCenterFormFilter;
            $formFilter->setDbAdapter($this->getServiceLocator()->get('Zend\Db\Adapter\Adapter'));
            $nameExclude = $formFilter->getInputFilter()->get('name')->getValidatorChain()->getValidators();
            $nameExclude[0]['instance']->setExclude(array('field'=>'id','value'=>$id));
            $form->setInputFilter($formFilter->getInputFilter());
            $form->setData($request->getPost());
            if($form->isValid()) {
                $this->getHelpCenterDao()->save($helpCenter);
                $this->flashMessenger()->addMessage('Edit Help center successfully');
			    return $this->redirect()->toRoute('help-center');
                
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
            return $this->redirect()->toRoute('help-center');
        }
        $helpCenter = $this->getHelpCenterDao()->fetchOne($id);
        if(!$helpCenter) {
            return $this->redirect()->toRoute('help-center');
        }
        $this->getHelpCenterDao()->delete($id);
        return $this->redirect()->toRoute('help-center');
    }
    
}