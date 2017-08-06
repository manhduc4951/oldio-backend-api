<?php
namespace Application\Controller\Plugin;

use Aws\Common\Aws;
use Aws\S3\BucketStyleListener;
use Aws\S3\S3Client;
use Aws\View\Exception\InvalidDomainNameException;
use Guzzle\Common\Event;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class GetObjectsUrl extends AbstractPlugin
{
    /**
     * @var S3Client
     */
    protected $client;

    /**
     * @var bool
     */
    protected $useSsl = true;

    /**
     * @var string
     */
    protected $defaultBucket;
    
    protected $serviceLocator;
    
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->client = $this->getServiceLocator()->get('Aws')->get('S3');
    }
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    /**
     * Set if HTTPS should be used for generating URLs
     *
     * @param bool $useSsl
     *
     * @return self
     */
    public function setUseSsl($useSsl)
    {
        $this->useSsl = (bool) $useSsl;

        return $this;
    }

    /**
     * Get if HTTPS should be used for generating URLs
     *
     * @return bool
     */
    public function getUseSsl()
    {
        return $this->useSsl;
    }

    /**
     * Set the default bucket to use if none is provided
     *
     * @param string $defaultBucket
     *
     * @return self
     */
    public function setDefaultBucket($defaultBucket)
    {  
        $this->defaultBucket = (string) $defaultBucket;

        return $this;
    }

    /**
     * Get the default bucket to use if none is provided
     *
     * @return string
     */
    public function getDefaultBucket()
    {
        if(!$this->defaultBucket) {
            $config = $this->getServiceLocator()->get('config');
            $this->defaultBucket = $config['config_ica467']['default_bucket'];    
        }
        return $this->defaultBucket;
    }
    
    public function getUrl($object, $bucket = '', $expiration = '')
    {
        $bucket = trim($bucket ?: $this->getDefaultBucket(), '/');
        if (empty($bucket)) {
            throw new InvalidDomainNameException('An empty bucket name was given');
        }
        // Create a command representing the get request
        // Using a command will make sure the configured regional endpoint is used
        $command = $this->client->getCommand('GetObject', array(
            'Bucket' => $bucket,
            'Key'    => $object,
        ));

        // Instead of executing the command, retrieve the request and make sure the scheme is set to what was specified
        $request = $command->prepare()->setScheme($this->useSsl ? 'https' : 'http')->setPort(null);

        // Ensure that the correct bucket URL style (virtual or path) is used based on the bucket name
        // This addresses a bug in versions of the SDK less than or equal to 2.3.4
        // @codeCoverageIgnoreStart
        if (version_compare(Aws::VERSION, '2.4.0', '<') && strpos($request->getHost(), $bucket) === false) {
            $bucketStyleListener = new BucketStyleListener();
            $bucketStyleListener->onCommandBeforeSend(new Event(array('command' => $command)));
        }
        // @codeCoverageIgnoreEnd

        if ($expiration) {
            return $this->client->createPresignedUrl($request, $expiration);
        } else {
            return $request->getUrl();
        }
    }
    
    /**
     * Get full url of objects which have file are stored on s3 amazon
     * 
     * @param mixed $objects
     * @param mixed $filePaths
     * @param string $type many(get objects) | one(get only one object)
     * @param string $subPath
     * @return
     */
    public function __invoke($objects,$filePaths = array(),$type = 'many',$subPath = '')
    {
        $config = $this->getServiceLocator()->get('config');
        $s3client = $this->getServiceLocator()->get('Aws')->get('S3');
        if($type == 'one') {
            foreach($filePaths as $attribute => $filePath) {
                if($objects["$attribute"] && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$config['config_ica467'][$filePath].'/'.$objects["$attribute"])) {
                    $objects["$attribute"] = $this->getUrl($config['config_ica467'][$filePath].'/'.$objects["$attribute"]);
                } else {
                    $objects["$attribute"] = null;
                }
            }
            return $objects;
                
        }
        $objects = $objects->toArray();
        foreach($objects as &$object){
            foreach($filePaths as $attribute => $filePath) {
                if($subPath) {
                    $file = $config['config_ica467'][$filePath].'/'.$subPath.'/'.$object["$attribute"];
                } else {
                    $file = $config['config_ica467'][$filePath].'/'.$object["$attribute"];
                }
                
                if($object["$attribute"] && $s3client->doesObjectExist($config['config_ica467']['default_bucket'],$file)) {
                    $object["$attribute"] = $this->getUrl($file);
                } else {
                    $object["$attribute"] = null;
                }
            }
        }
        return $objects;
    }
    
}