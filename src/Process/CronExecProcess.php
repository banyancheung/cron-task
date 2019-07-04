<?php declare(strict_types=1);

namespace Swoft\CronTask\Process;

use Swoft\Co;
use Swoft\CronTask\Crontab\Crontab;
use Swoft\Task\Task;
use Swoft\Bean\BeanFactory;
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
        $server = \server()->getSwooleServer();
        $process->name(' cronexec process');
        /** @var \Swoft\CronTask\Crontab\Crontab $cron */
        $cron = Crontab::getInstance();
        $server->tick(500, function () use ($cron) {
            $tasks = $cron->getExecTasks();
            if (!empty($tasks)) {
                foreach ($tasks as $task) {
                    // Diliver task
                    Task::async($task['task'], $task['taskMethod']);
                    $cron->finishTask($task['key']);
                }
            }
        });
    }

}
