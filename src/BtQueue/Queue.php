<?php
/**
 * Created by PhpStorm.
 * User: cgp
 * Date: 2017/5/1
 * Time: 09:58
 */

namespace BtQueue;

use Pheanstalk\Pheanstalk;

class BtQueue
{
    public static function push($queueName, $jobName, $args, $delay=0) {
        $beanstalk = new Pheanstalk('127.0.0.1', '11301', '2');
        $data      =  array(
            'class' => $jobName,
            'args'  => $args,
        );
        $jobId     =  $beanstalk->useTube($queueName)->put(json_encode($data), 1024, $delay, 2);
        return $jobId;
    }

}