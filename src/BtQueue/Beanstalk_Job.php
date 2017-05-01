<?php
/**
 * Created by PhpStorm.
 * User: cgp
 * Date: 2017/5/1
 * Time: 10:18
 */

namespace BtQueue;
class Beanstalk_Job
{
    private $instance;
    public function __construct($queue, $payload)
    {
        $this->queue   = $queue;
        $this->payload = $payload;
    }
    public static function reserve($queue, $data)
    {
        if(!is_object($data)) {
            return false;
        }
        return new Beanstalk_Job($queue, $data);
    }
    public function getInstance()
    {
        if (!is_null($this->instance)) {
            return $this->instance;
        }
        if(!class_exists($this->payload->class)) {
            throw new Beanstalk_Exception(
                'Could not find job class ' . $this->payload['class'] . '.'
            );
        }

        if(!method_exists($this->payload->class, 'perform')) {
            throw new Beanstalk_Exception(
                'Job class ' . $this->payload['class'] . ' does not contain a perform method.'
            );
        }
        $this->instance        = new $this->payload->class;
        //$this->instance->job   = $this;
        $this->instance->args  = $this->getArguments();
        //$this->instance->queue = $this->queue;
        return $this->instance;
    }
    public function getArguments()
    {
        if (!isset($this->payload->args)) {
            return array();
        }
        print_r($this->payload->args);
        return $this->payload->args;
    }

    public function perform()
    {
        try {
            $instance = $this->getInstance();
            //if(method_exists($instance, 'setUp')) {
            //    $instance->setUp();
            //}
            return $instance->perform();
            //if(method_exists($instance, 'tearDown')) {
            //    $instance->tearDown();
            //}
        }
        // beforePerform/setUp have said don't perform this job. Return.
        catch(Beanstalk_Job_DontPerform $e) {
            return false;
        }
    }
}