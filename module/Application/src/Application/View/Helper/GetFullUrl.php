<?php
namespace Application\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * Using to get full url of a file (using only if content store in local server)
 */ 
class GetFullUrl extends AbstractHelper
{
    protected $serviceLocator;
    
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    /**
     * Get full url from view (to show image, file...link)
     * 
     * @param mixed $route
     * @param mixed $file
     * @return
     */
    public function __invoke($route,$file)
	{ 
       $url = $this->getServiceLocator()->get('ControllerPluginManager')->get('Url');
       return $url->fromRoute($route,array('file' => $file),array('force_canonical' => true));
	}
}