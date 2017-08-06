<?php

namespace Api\Authentication\Adapter;

use Zend\Authentication\Adapter\AbstractAdapter;
use Api\Authentication\OAuth2Result;

/**
 * OAuth authenticate adapter
 * 
 * @package Api_Authenticate;
 * @author duyld
 */
class OAuth2 extends AbstractAdapter
{
    /**
     * oauth server object.
     * 
     * @var \OAuth2_Server
     */
    protected $server;
    
    protected $request;
    
    /**
     * The constructor.
     * 
     * @param \OAuth2_Server $server
     * @return void
     */
    public function __construct(\OAuth2_Server $server)
    {
        $this->setServer($server);
    }
    
    public function getRequest()
    {
        return $this->request;
    }
    
    public function setRequest($request)
    {
        $this->request = $request;
    }
    
    /**
     * Set oauth server object.
     * 
     * @param \OAuth2_Server $server
     * @return \Api\Authentication\Adapter\OAuth2
     */
    public function setServer($server)
    {
        $this->server = $server;
        
        return $this;
    }
    
    /**
     * Get oauth server object.
     * 
     * @return \OAuth2_Server
     */
    public function getServer()
    {
        return $this->server;
    }
    
    /**
     * Performs an authentication attempt
     *
     * @return \Api\Authentication\OAuth2Result
     * @throws \Zend\Authentication\Adapter\Exception\ExceptionInterface If authentication cannot be performed
     */
    public function authenticate()
    {
        if($this->getRequest()) {
            $request = $this->getRequest();
        } else {
            $request = \OAuth2_Request::createFromGlobals();
        }
        $response = $this->getServer()->handleTokenRequest(
            $request,
            new \OAuth2_Response()
        );
        
        return new OAuth2Result($response);
    }
}