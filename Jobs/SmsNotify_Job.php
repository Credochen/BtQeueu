<?php
/**
 * Created by PhpStorm.
 * User: Guangpeng Chen
 * Date: 2017/5/1
 * Time: 10:11
 */
class SmsNotify_Job {
    public function perform()
    {
        print_r($this->args);
        echo "\n\n Send Sms！！\n\n";
        return true;
    }
}