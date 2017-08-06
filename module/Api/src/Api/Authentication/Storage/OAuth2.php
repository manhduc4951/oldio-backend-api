<?php

namespace Api\Authentication\Storage;

use Zend\Authentication\Storage\StorageInterface;
use User\Model\Dao\UserDao;

/**
 * OAuth authenticate storage
 *
 * @package Api_Authenticate;
 * @author duyld
 */
class OAuth2 implements StorageInterface
{
    /**
     * oauth server object.
     *
     * @var \OAuth2_Server
     */
    protected $server;
    
    /**
     * User dao object.
     *
     * @var \User\Model\Dao\UserDao
     */
    protected $userDao;
    
    /**
     * The identity used in the authentication attempt
     *
     * @var mixed
     */
    protected $identity;
    
    /**
     * The constructor.
     *
     * @param \OAuth2_Server $server
     * @param \User\Model\Dao\UserDao $userDao
     * @return void
     */
    public function __construct(\OAuth2_Server $server, UserDao $userDao)
    {
        $this->userDao = $userDao;
        $this->server = $server;
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
     * Get user dao
     * 
     * @return \User\Model\Dao\UserDao
     */
    public function getUserDao()
    {
        return $this->userDao;
    }
    
    /**
     * Returns true if and only if storage is empty
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface If it is impossible to determine whether storage is empty
     * @return bool
     */
    public function isEmpty()
    {
        return ! $this->read();
    }
    
    /**
     * Returns the contents of storage
     *
     * Behavior is undefined when storage is empty.
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface If reading contents from storage is impossible
     * @return mixed
    */
    public function read()
    {
        if (null === $this->identity) {
            $verify = $this->getServer()->verifyResourceRequest(
                \OAuth2_Request::createFromGlobals(),
                new \OAuth2_Response()
            );
            
            if ( ! $verify) {
                $this->identity = false;
            } else {
                // retrieve the identity from database
                $this->identity = $this->getUserDao()->fetchOne($verify['user_id']);
            }
        }
        
        return $this->identity;
    }
    
    /**
     * Writes $contents to storage
     *
     * @param  mixed $contents
     * @throws \Zend\Authentication\Exception\ExceptionInterface If writing $contents to storage is impossible
     * @return void
    */
    public function write($contents)
    {
        
    }
    
    /**
     * Clears contents from storage
     *
     * @throws \Zend\Authentication\Exception\ExceptionInterface If clearing contents from storage is impossible
     * @return void
    */
    public function clear()
    {
        
    }
}