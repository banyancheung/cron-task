<?php declare(strict_types=1);


namespace Swoft\CronTask\Swoole;


use function go;
use Swoft;
use Swoft\Server\Swoole\PipeMessageInterface;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\Server\Swoole\SwooleEvent;
use Swoole\Server;

/**
 * Class PipeMessageListener
 *
 * @since 2.0
 *
 * @Bean()
 */
class PipeMessageListener implements PipeMessageInterface
{
    /**
     * @param Server $server
     * @param int $taskId
     * @param string $data
     */
    public function onPipeMessage(Server $server, int $srcWorkerId, $message): void
    {
        go(function () use ($server, $srcWorkerId, $message) {
            Swoft::trigger(SwooleEvent::PIPE_MESSAGE, null, $server, $srcWorkerId, $message);
        });
    }
}