<?php
namespace User\Controller;

use Api\Controller\AbstractMyRestfulController;
use Zend\View\Model\JsonModel;

class CountryRestController extends AbstractMyRestfulController
{
    protected $countryDao;
    
    public function getcountryDao()
    {
        if(!$this->countryDao) {
            $sm = $this->getServiceLocator();
            $this->countryDao = $sm->get('User\Model\CountryDao');
        }
        return $this->countryDao;
    }
    
    public function getList()
    {
        $countries = $this->getcountryDao()->fetchAll()->toArray();
        echo '<pre>'; var_dump($countries); echo '</pre>'; die;
    }

    public function get($id) {}

    public function create($data) {}

    public function update($id, $data) {}

    public function delete($id) {}

}
