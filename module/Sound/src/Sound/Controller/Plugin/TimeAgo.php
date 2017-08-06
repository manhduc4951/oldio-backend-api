<?php
namespace Sound\Controller\Plugin;

use Zend\Mvc\Controller\Plugin\AbstractPlugin;

class TimeAgo extends AbstractPlugin
{   
    public function __invoke($ptime)
    {
        if(is_string($ptime)) {
            $ptime = strtotime($ptime);
        }
        $etime = time() - $ptime;
        //return $etime;

        if( $etime < 1 )
        {
            return 'less than 1 second ago';
        }
    
        $a = array( 12 * 30 * 24 * 60 * 60  =>  'year',
                    30 * 24 * 60 * 60       =>  'month',
                    24 * 60 * 60            =>  'day',
                    60 * 60             =>  'hour',
                    60                  =>  'minute',
                    1                   =>  'second'
        );
    
        foreach( $a as $secs => $str )
        {
            $d = $etime / $secs;
    
            if( $d >= 1 )
            {
                $r = round( $d );
                return 'about ' . $r . ' ' . $str . ( $r > 1 ? 's' : '' ) . ' ago';
            }
        }
        
    }
    
}