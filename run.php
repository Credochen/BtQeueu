<?php
/**
 * Created by PhpStorm.
 * User: Guangpeng Chen
 * Date: 2017/5/1
 * Time: 09:41
 */
include './vendor/autoload.php';
include './Jobs/SmsNotify_Job.php';
include './Jobs/WxNotify_Job.php';

//Picking up things from the queue
$worker = new BtQueue\Beanstalk_Worker('Notify');
$worker->run();