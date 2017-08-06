<?php

namespace Api\Authentication;

use Zend\Authentication\Result;

/**
 * Oauth 2 authenticate result object.
 * 
 * It is an adapter for OAuth2_Response.
 * 
 * @package Api_Authentication
 * @author duyld
 */
class OAuth2Result extends Result
{
    /**
     * OAuth response object
     * 
     * @var \OAuth2_Response
     */
    protected $response;
    
    /**
     * The constructor.
     * 
     * @param \OAuth2_Response $response
     * @return void
     */
    public function __construct(\OAuth2_Response $response)
    {
        $this->response = $response;
    }
    
    /**
     * Returns whether the result represents a successful authentication attempt
     *
     * @return bool
     */
    public function isValid()
    {
        return $this->response->isSuccessful();
    }
    
    /**
     * Returns an array of string reasons why the authentication attempt was unsuccessful
     *
     * If authentication was successful, this method returns an empty array.
     *
     * @return array
     */
    public function getMessages()
    {
        return array($this->response->getParameter('error_description'));
    }
    
    /**
     * Get the access token for access resources.
     * 
     * @return string
     */
    public function getAccessToken()
    {
        return $this->response->getParameter('access_token');
    }
    
    /**
     * Get the refresh token for access resources.
     * 
     * @return string
     */
    public function getRefreshToken()
    {
        return $this->response->getParameter('refresh_token');
    }
    
    /**
     * Get Expired time.
     * 
     * @return int
     */
    public function getExpiresIn()
    {
        return $this->response->getParameter('expires_in');
    }
}