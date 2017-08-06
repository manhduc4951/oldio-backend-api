<?php
namespace SoundSet\Controller;

use Zend\Mvc\Controller\AbstractActionController;

class SoundSetController extends AbstractActionController
{
    protected $soundSetDao;
    
    protected $soundSetItemDao;
    
    protected $soundSetBusiness;
    
    public function getSoundSetDao()
    {
        if(!$this->soundSetDao) {
            $sm = $this->getServiceLocator();
            $this->soundSetDao = $sm->get('SoundSet\Model\SoundSetDao');
        }
        return $this->soundSetDao;
    }
    
    public function getSoundSetItemDao()
    {
        if(!$this->soundSetItemDao) {
            $sm = $this->getServiceLocator();
            $this->soundSetItemDao = $sm->get('SoundSet\Model\SoundSetItemDao');
        }
        return $this->soundSetItemDao;
    }
    
    public function getSoundSetBusiness()
    {
        if(!$this->soundSetBusiness) {
            $sm = $this->getServiceLocator();
            $this->soundSetBusiness = $sm->get('SoundSet\Business\SoundSetBussiness');
        }
        return $this->soundSetBusiness;
    }
    
    public function indexAction()
    {
        $config = $this->getServiceLocator()->get('Config');        
        
        /*Fetch data && paginate*/
        $request = $this->getRequest();
        $query = null;
        if($request->isGet()) {            
            $query = $this->params()->fromQuery();
            if(!empty($query['created_at'])) {
                $created = explode(' - ',$query['created_at']);
                $query['created_at_from'] = (!empty($created[0])) ? $created[0].' 00:00:00' : null;
                $query['created_at_to'] = (!empty($created[1])) ? $created[1].' 23:59:59' : null;
            }
        }
        $data = $this->getSoundSetDao()->fetchAllSoundSet(true,$query);
        $paginator = $data['paginator'];        
        $paginator->setCurrentPageNumber((int)$this->params()->fromQuery('page', 1));        
        $paginator->setItemCountPerPage($config['config_ica467']['item_per_page']);
        $page = (int)$this->params()->fromQuery('page', 1);
        
        /*Form search*/
        $formSearch = new \SoundSet\Form\SoundSetFormSearch;
        if($data['query']) {
            $formSearch->setData($data['query']);
        }
        
        /*Flash message*/
        $messages = '';
        $flashMessenger = $this->flashMessenger();
        if ($flashMessenger->hasMessages()) {
               $messages = $flashMessenger->getMessages();
        }
        
        return array(
            'soundSets' => $paginator,
            'messages' => $messages,
            'formSearch' => $formSearch,
            'permissions' => $this->permissionActions(array('add','edit','delete','view'),'soundset_soundset'),
        );
        
    }
    
    public function addAction()
    {  
        $form = new \SoundSet\Form\SoundSetForm;
        $request = $this->getRequest();
        if($request->isPost()) {
            $formFilter = new \SoundSet\Form\SoundSetFormFilter;
            $formFilter->setServiceLocator($this->getServiceLocator());
            $form->setInputFilter($formFilter->getInputFilter());
            $data = array_merge($request->getPost()->toArray(),$_FILES);
            $form->setData($data);
            if($form->isValid()) {
                $soundSet = new \SoundSet\Model\Dto\SoundSetDto;
                $soundSet->exchangeArray($form->getData());
                $this->getSoundSetBusiness()->createSoundSet($soundSet);
                $this->flashMessenger()->addMessage('Create soundset successfully');
                return $this->redirect()->toRoute('sound-set');
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
            return $this->redirect()->toRoute('sound-set',array('action' => 'add'));
        }
        $soundSet = $this->getSoundSetDao()->fetchOne($id);
        if(!$soundSet) {
            return $this->redirect()->toRoute('sound-set');
        }
        $request = $this->getRequest();
        $oldImage = $soundSet->image;
        $oldZipFile = $soundSet->zip_file;
        $form = new \SoundSet\Form\SoundSetForm;
        $form->bind($soundSet);
        if($request->isPost()) {
            $formFilter = new \SoundSet\Form\SoundSetFormFilter;
            $formFilter->setServiceLocator($this->getServiceLocator());
            $formFilter->getInputFilter()->get('zip_file')->setAllowEmpty(true);
            $nameExclude = $formFilter->getInputFilter()->get('name')->getValidatorChain()->getValidators();
            $nameExclude[0]['instance']->setExclude(array('field'=>'id','value'=>$id));
            $form->setInputFilter($formFilter->getInputFilter());
            $data = array_merge($request->getPost()->toArray(),$_FILES);
            $form->setData($data);
            if($form->isValid()) {
                $soundSet->old_image = $oldImage;
                $soundSet->old_zip_file = $oldZipFile;
                $this->getSoundSetBusiness()->updateSoundSet($soundSet);
                $this->flashMessenger()->addMessage('Update soundset successfully');
                return $this->redirect()->toRoute('sound-set');
                
            }
        }
        
        $config = $this->getServiceLocator()->get('config');
        $image = ($soundSet->image) ? $this->getS3Url($config['config_ica467']['sound_set_image_upload_s3'].'/'.$soundSet->image) : null;
        return array(
            'form' => $form,
            'id' => $id,
            'files' => array(
                'image' => $image,
            ),
        );
    }
    
    public function deleteAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('sound-set');
        }
        $soundSet = $this->getSoundSetDao()->fetchOne($id);
        if(!$soundSet) {
            return $this->redirect()->toRoute('sound-set');
        }
        $this->getSoundSetBusiness()->deleteSoundSet($soundSet);
        $this->flashMessenger()->addMessage('Delete soundset successfully');
        return $this->redirect()->toRoute('sound-set');
    }
    
    public function viewAction()
    {
        $id = $this->params()->fromRoute('id',0);
        if(!$id) {
            return $this->redirect()->toRoute('sound-set');
        }
        $soundSet = $this->getSoundSetDao()->fetchOne($id);
        if(!$soundSet) {
            return $this->redirect()->toRoute('sound-set');
        }
        $soundSetItems = $this->getSoundSetItemDao()->fetchAllBy('sound_set_id',$id);
        return array(
            'soundSetItems' => $soundSetItems,
        );
    }
    
}