<?php
namespace Application\View\Helper;

use Aws\Common\Aws;
use Aws\S3\BucketStyleListener;
use Aws\S3\S3Client;
use Aws\View\Exception\InvalidDomainNameException;
use Guzzle\Common\Event;
use Zend\View\Helper\AbstractHelper;

/**
 * View helper that can render a link to a S3 object. It can also create signed URLs
 */
class GetS3Url extends AbstractHelper
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
    
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }
    
    public function __construct($serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;
        $this->client = $this->getServiceLocator()->get('Aws')->get('S3');
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

    /**
     * Create a link to a S3 object from a bucket. If expiration is not empty, then it is used to create
     * a signed URL
     *
     * @param  string     $object The object name (file name)
     * @param  string     $target Target (path) to file
     * @param  string     $bucket The bucket name
     * @param  string|int $expiration The Unix timestamp to expire at or a string that can be evaluated by strtotime
     * @throws InvalidDomainNameException
     * @return string
     */
    public function __invoke($object, $target, $sub_target = '', $bucket = '', $expiration = '')
    {
        $bucket = trim($bucket ?: $this->getDefaultBucket(), '/');
        if (empty($bucket)) {
            throw new InvalidDomainNameException('An empty bucket name was given');
        }
        $config = $this->getServiceLocator()->get('config');
        $target = $config['config_ica467'][$target];
        // Create a command representing the get request
        // Using a command will make sure the configured regional endpoint is used
        $key = (!$sub_target) ? $target.'/'.$object : $target.'/'.$sub_target.'/'.$object;
        $command = $this->client->getCommand('GetObject', array(
            'Bucket' => $bucket,
            //'Key'    => $object,
            'Key' => $key,
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
}
