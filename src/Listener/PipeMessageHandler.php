<?php declare(strict_types=1);

namespace Swoft\CronTask\Listener;

use Swoft\CronTask\PipeMessage;
use Swoft\Event\Annotation\Mapping\Listener;
use Swoft\Event\EventHandlerInterface;
use Swoft\Server\Swoole\SwooleEvent;
use Swoft\Event\EventInterface;
use Swoft\Task\Task;

/**
 * The pipe message listener
 *
 * @Listener(event=SwooleEvent::PIPE_MESSAGE)
 */
class PipeMessageHandler implements EventHandlerInterface
{

    public function handle(EventInterface $event): void
    {
        $params = $event->getParams();
        if (count($params) < 3) {
            return;
        }
        list($server, $srcWorkerId, $data) = $params;
        $message = PipeMessage::unpack($data);
        $type = $message['type'];
        $taskName = $message['message']['name'];
        $methodName = $message['message']['method'];
        // delever task
        Task::async($taskName, $methodName);

    }
}