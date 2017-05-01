<?php
/**
 * Created by PhpStorm.
 * User: Guangpeng Chen
 * Date: 2017/5/1
 * Time: 09:32
 */
include './vendor/autoload.php';
// 投放短信发送定时器
$delay = 0;
$data = array(
    'mobile' => '18750193275',
    'id'     => '11',
    'time'   => '2017-05-01 10:30:00'
);
$res = \BtQueue\BtQueue::push('Notify', 'SmsNotify_Job', $data);
var_dump($res);
$res = \BtQueue\BtQueue::push('Notify', 'WxNotify_Job', $data);
var_dump($res);
//$delay = 0;//(int) strtotime($data['time']) - time();
//$res = $beanstalk->useTube('Sms')->put(json_encode($data), 1024, $delay, 2);
