<?php
/**
 * Created by PhpStorm.
 * User: cgp
 * Date: 2017/5/1
 * Time: 10:24
 */

namespace BtQueue;
use Pheanstalk\Pheanstalk;
class Beanstalk_Worker {

    private $path;
    private $queues = [];
    public function __construct($queues, Array $config=[]) {
        if (!is_array($queues)) {
            $this->queues[] = $queues;
        }
        $defaults = [
            'persistent' => true,
            'host' => '127.0.0.1',
            'port' => 11300,
            'timeout' => 1,
        ];
        $config = $config + $defaults;
        $this->log('starting');
        $this->pheanstalk = new Pheanstalk($config['host'], $config['port'], $config['timeout'], $config['persistent']);
    }

    public function __destruct() {
        $this->log('ending');
    }

    private function setBasePath($path) {
        $this->path = $path;
    }
    private function reverse()
    {
        foreach ($this->queues as $queue) {
            $this->log("Checking {$queue} for jobs");
            $job = $this->pheanstalk->watch($queue)->ignore('default')->reserve();
            print_r($job);
            if($job) {
                return ['queue'=>$queue, 'job'=>$job];
            }
        }
        return false;
    }
    public function perform(Beanstalk_Job $job)
    {
        return $job->perform();
        //$job->updateStatus(Resque_Job_Status::STATUS_COMPLETE);
        //$this->logger->log(LogLevel::NOTICE, '{job} has finished', array('job' => $job));
    }
    public function run() {
        $this->log('starting to run');
        $done_jobs = array();
        while(1) {
            $jobData = $this->reverse();
            if ($jobData=== false) {
                $this->log("No job found,sleep 1s");
                sleep(1);
                continue;
            }
            $job = $jobData['job'];
            $job_encoded = json_decode($job->getData(), false);
            $jobInstance = Beanstalk_Job::reserve($jobData['queue'], $job_encoded);
            $this->log("Found job on {$job_encoded->queue}");
            $result = $this->perform($jobInstance);
            if ($result) {
                $this->log('job:'.print_r($job_encoded, 1));
                $this->pheanstalk->delete($job);
                unset($job_encoded);
                unset($result);
                unset($job);
            }
            $memory = memory_get_usage();
            $this->log('memory:' . $memory);
            if($memory > 1000000) {
                $this->log('exiting run due to memory limit');
                exit;
            }
            usleep(10);
        }
    }
    private function log($txt) {
        echo date('Y-m-d H:i:s'), ':', $txt, "\n";
        //file_put_contents($this->path . '/worker.txt', $txt . "\n", FILE_APPEND);
    }
}