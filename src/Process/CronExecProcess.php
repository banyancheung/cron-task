<?php declare(strict_types=1);

namespace Swoft\CronTask\Process;

use Swoft\Task\Task;
use Swoole\Process;
use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Crontab process
 *
 * @Bean()
 */
class CronExecProcess
{

    public function create(): Process
    {
        $process = new Process([$this, 'handle']);
        return $process;
    }

    public function handle(Process $process)
    {
        $server = \server();
        $pname = $server->getPid();
        echo 'pname:' . $pname;
        $process->name(sprintf('%s cronexec process', $pname));
        /** @var \Swoft\CronTask\Crontab\Crontab $cron */
        $cron = \bean('crontab');
        // Swoole/HttpServer
        $server->tick(0.5 * 1000, function () use ($cron) {
            $tasks = $cron->getExecTasks();
            if (!empty($tasks)) {
                foreach ($tasks as $task) {
                    // Diliver task
                    Task::async($task['taskClass'], $task['taskMethod']);
                    $cron->finishTask($task['key']);
                }
            }
        });
    }

}
