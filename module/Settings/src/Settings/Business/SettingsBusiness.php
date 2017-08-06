<?php
namespace Settings\Business;

use Zend\Di\ServiceLocator;
use Zend\ServiceManager\ServiceManager;
use Zend\ServiceManager\ServiceManagerAwareInterface;
use Zend\Mail\Message;
use Zend\Mail\Transport\Smtp as SmtpTransport;
use Zend\Mail\Transport\SmtpOptions;
use ZendService\Apple\Apns\Client\Message as Client;
use ZendService\Apple\Apns\Message as PushMessage;
use ZendService\Apple\Apns\Message\Alert;
use ZendService\Apple\Apns\Response\Message as Response;
use ZendService\Apple\Apns\Exception\RuntimeException;

class SettingsBusiness implements ServiceManagerAwareInterface
{	
 	protected $serviceManager;
    
    protected $partial;
    
 	public function setServiceManager(ServiceManager $serviceManager)
 	{
 	    $this->serviceManager = $serviceManager;
 	}
 	
 	public function getServiceManager()
 	{
 	    return $this->serviceManager;
 	}
    
    public function getPartial()
    {
        if(!$this->partial) {
            $this->partial = $this->getServiceManager()->get('viewhelpermanager')->get('partial');
        }
        return $this->partial;
    }
    
    public function sendEmail($data = array())
    {  
        $email = (!empty($data['email'])) ? $data['email'] : null;
        $emailSubject = (!empty($data['email_subject'])) ? $data['email_subject'] : null;
        $emailBody = (!empty($data['email_body'])) ? $data['email_body'] : null;
        $config = $this->getServiceManager()->get('config');
        $emailTemplate = $this->getPartial()->__invoke('settings/partial/email-template.phtml',array('content' => $emailBody));
        
        $mailmsg = new Message();
        /*Set email body to html*/
        $html = new \Zend\Mime\Part($emailTemplate);
        $html->type = 'text/html';
        $body = new \Zend\Mime\Message;
        $body->setParts(array($html));
        $mailmsg->addFrom($config['config_ica467']['email_send_forgot_password'])
                ->addTo($email)
                ->setSubject($emailSubject)
                ->setBody($body);
        
        //Setup SMTP transport
        $transport = new SmtpTransport();
        $options   = new SmtpOptions(array(
            'name'              => 'gmailSMTP',
            'host'              => 'smtp.gmail.com',
            'connection_class'  => 'plain',
            'connection_config' => array(
                'username' => $config['config_ica467']['email_send_forgot_password'],
                'password' => $config['config_ica467']['password_email_send_forgot_password'],
                'ssl' => 'tls'
            ),
        ));
        $transport->setOptions($options);
        $transport->send($mailmsg);
       
    }
    
    public function pushNotification($data = array())
    {
        $config = $this->getServiceManager()->get('config');
        $deviceToken = (!empty($data['device_token'])) ? $data['device_token'] : null;
        $alert = (!empty($data['alert'])) ? $data['alert'] : null;
        $custom = (isset($data['custom'])) ? $data['custom'] : null;
        $badge = (isset($data['badge'])) ? $data['badge'] : null;
        
        $client = new Client();
        $client->open(Client::PRODUCTION_URI, $config['config_ica467']['certificate_apns'], '123456');
        $message = new PushMessage();
        $message->setId('HEyzc0Vc3qNjywoKxMdrg65WVQTHlKEfR996n6b');
        //$message->setToken('380d05469707d352c59ceff9606422141250bcb936ed5392bffe288058b9cf6c');
        $message->setToken($deviceToken);
        $message->setSound('bingbong.aiff');
        $message->setAlert($alert);
        if($custom) {
            $message->setCustom($custom);
        }
        if($badge) {
            $message->setBadge($badge);
        }
        
        try {
            $response = $client->send($message);
        } catch (RuntimeException $e) {
            echo $e->getMessage() . PHP_EOL;
            exit(1);
        }
        $client->close();
        //file_put_contents('public/log.txt', $response->getCode().PHP_EOL,FILE_APPEND);
        if ($response->getCode() != Response::RESULT_OK) {
             switch ($response->getCode()) {
                 case Response::RESULT_PROCESSING_ERROR:
                     // you may want to retry
                     break;
                 case Response::RESULT_MISSING_TOKEN:
                     // you were missing a token
                     break;
                 case Response::RESULT_MISSING_TOPIC:
                     // you are missing a message id
                     break;
                 case Response::RESULT_MISSING_PAYLOAD:
                     // you need to send a payload
                     break;
                 case Response::RESULT_INVALID_TOKEN_SIZE:
                     // the token provided was not of the proper size
                     break;
                 case Response::RESULT_INVALID_TOPIC_SIZE:
                     // the topic was too long
                     break;
                 case Response::RESULT_INVALID_PAYLOAD_SIZE:
                     // the payload was too large
                     break;
                 case Response::RESULT_INVALID_TOKEN:
                     // the token was invalid; remove it from your system
                     break;
                 case Response::RESULT_UNKNOWN_ERROR:
                     // apple didn't tell us what happened
                     break;
             }
        }
    }
    
    /**
     * Separate like, comment, follow into 2 threads to increase speed
     * 
     * @param mixed $data
     * @return void
     */
    public function push(array $data = array())
    {  
        $urlPlugin = $this->getServiceManager()->get('ControllerPluginManager')->get('Url');
        $api = $urlPlugin->fromRoute('push-rest',array(),array('force_canonical' => true));
        $tuCurl = curl_init($api);
        //$headers = array();
        //$headers[] = 'Authorization: Bearer ' . '0ca67d6c35553a9d09b112b39b29368cd05e787e';
        curl_setopt($tuCurl, CURLOPT_RETURNTRANSFER, 0);
        curl_setopt($tuCurl, CURLOPT_FRESH_CONNECT, true);
        curl_setopt($tuCurl, CURLOPT_TIMEOUT, 1);
        curl_setopt($tuCurl, CURLOPT_POST, true);
        //curl_setopt($tuCurl, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($tuCurl, CURLOPT_POSTFIELDS, $data); 
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYHOST, 0);
        curl_setopt($tuCurl, CURLOPT_SSL_VERIFYPEER, 0);
        curl_exec($tuCurl);
        curl_close($tuCurl); 
    }
    
//    public function push2()
//    {
//        // Put your device token here (without spaces):
//        $deviceToken = '380d05469707d352c59ceff9606422141250bcb936ed5392bffe288058b9cf6c';
//        
//        // Put your private key's passphrase here:
//        $passphrase = '123456';
//        
//        // Put your alert message here:
//        $message = 'Push notification!';
//        
//        ////////////////////////////////////////////////////////////////////////////////
//        
//        $ctx = stream_context_create();
//        stream_context_set_option($ctx, 'ssl', 'local_cert', 'public/ck.pem');
//        stream_context_set_option($ctx, 'ssl', 'passphrase', $passphrase);
//        
//        // Open a connection to the APNS server
//        $fp = stream_socket_client(
//        	'ssl://gateway.sandbox.push.apple.com:2195', $err,
//        	$errstr, 60, STREAM_CLIENT_CONNECT|STREAM_CLIENT_PERSISTENT, $ctx);
//        
//        if (!$fp)
//        	exit("Failed to connect: $err $errstr" . PHP_EOL);
//        
//        echo 'Connected to APNS' . PHP_EOL;
//        
//        // Create the payload body
//        $body['aps'] = array(
//        	'alert' => $message,
//        	'sound' => 'default'
//        	);
//        
//        // Encode the payload as JSON
//        $payload = json_encode($body);
//        
//        // Build the binary notification
//        $msg = chr(0) . pack('n', 32) . pack('H*', $deviceToken) . pack('n', strlen($payload)) . $payload;
//        
//        // Send it to the server
//        $result = fwrite($fp, $msg, strlen($msg));
//        
//        if (!$result)
//        	echo 'Message not delivered' . PHP_EOL;
//        else
//        	echo 'Message successfully delivered' . PHP_EOL;
//        
//        // Close the connection to the server
//        fclose($fp);
//    }
    
}