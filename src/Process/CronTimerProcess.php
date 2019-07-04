<?php declare(strict_types=1);

namespace Swoft\CronTask\Process;

use Swoft\Co;
use Swoft\CronTask\Crontab\Crontab;
use Swoole\Process;
use Swoft\Bean\BeanFactory;
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
        $server = \server()->getSwooleServer();
        $process->name('crontimer process');
        /* @var \Swoft\CronTask\Crontab\Crontab $cron */
        $cron = Crontab::getInstance();
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
