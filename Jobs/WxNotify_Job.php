<?php
/**
 * Created by PhpStorm.
 * User: Guangpeng Chen
 * Date: 2017/5/1
 * Time: 10:11
 */
class WxNotify_Job {
    public function perform()
    {
        print_r($this->args);
        echo "\n\n Send Wechat！！\n\n";
        return true;
    }
}