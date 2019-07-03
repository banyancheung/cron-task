<?php declare(strict_types=1);

namespace Swoft\CronTask\Process;

use Swoole\Process;
use Swoft\Bean\Annotation\Mapping\Bean;

/**
 * Crontab timer process
 * @Bean()
 */
class CronTimerProcess
{

    public function create(): Process
    {
        $process = new Process([$this, 'handle']);
        return $process;
    }

    public function handle(Process $process)
    {
        // SwooleServer
        $server = \server();
        $pname = \server()->getPid();
        echo 'pname:' . $pname;
        $process->name(sprintf('%s crontimer process', $pname));
        /* @var \Swoft\CronTask\Crontab\Crontab $cron */
        $cron = \bean('crontab');
        $time = (60 - date('s')) * 1000;
        $server->after($time, function () use ($server, $cron) {
            // Every minute check all tasks, and prepare the tasks that next execution point needs
            $cron->checkTask();
            $server->tick(60 * 1000, function () use ($cron) {
                $cron->checkTask();
            });
        });
    }

}
