<?php declare(strict_types=1);

namespace Swoft\CronTask\Listener;

use Swoft\Bean\BeanFactory;
use Swoft\CronTask\Crontab\Crontab;
use Swoft\CronTask\Crontab\TableCrontab;
use Swoft\CronTask\Process\CronExecProcess;
use Swoft\CronTask\Process\CronTimerProcess;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Event\EventInterface;
use Swoft\Server\ServerEvent;

/**
 * Class AttachCronProcessHandler
 *
 * @since 2.0
 *
 * @Listener(event=ServerEvent::BEFORE_START)
 */
class AttachCronProcessHandler implements EventHandlerInterface
{

    /**
     * @param EventInterface $event
     *
     * @throws \ReflectionException
     * @throws \Swoft\Bean\Exception\ContainerException
     */
    public function handle(EventInterface $event): void
    {
        $setting = \config('cron');
        // Init crontab share memory table
        if (isset($setting['cronable']) && (int)$setting['cronable'] === 1) {
            $this->initCrontabMemoryTable();
            Crontab::init();
            $this->initProcessByEvent($event);
        }
    }

    /**
     * init table of crontab
     */
    private function initCrontabMemoryTable(): void
    {
        /** @var array[] $settings */
        $setting = \config('cron');
        $taskCount = isset($setting['task_count']) && $setting['task_count'] > 0 ? $setting['task_count'] : null;
        $taskQueue = isset($setting['task_queue']) && $setting['task_queue'] > 0 ? $setting['task_queue'] : null;
        TableCrontab::init($taskCount, $taskQueue);
    }

    /**
     *
     * @param EventInterface $event
     */
    private function initProcessByEvent(EventInterface $event): void
    {
        $swooleServer = $event->getTarget()->getSwooleServer();
        $execProcess = \bean(CronExecProcess::class)->create();
        $timerProcess = \bean(CronTimerProcess::class)->create();
        $swooleServer->addProcess($execProcess);
        $swooleServer->addProcess($timerProcess);
    }

}
