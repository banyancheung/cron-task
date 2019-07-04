<?php declare(strict_types=1);

namespace Swoft\CronTask;


use Swoft\Stdlib\Helper\JsonHelper;

/**
 * Pipe message
 */
class PipeMessage
{
    /**
     * Task message type
     */
    const MESSAGE_TYPE_TASK = 'task';


    /**
     * @param string $type
     * @param array $data
     *
     * @return string
     */
    public static function pack(string $type, array $data): string
    {
        $data = [
            'type' => $type,
            'message' => $data,
        ];
        return JsonHelper::encode($data, JSON_UNESCAPED_UNICODE);
    }

    /**
     * @param string $message
     *
     * @return array
     */
    public static function unpack(string $message): array
    {
        $messageAry = json_decode($message, true);
        return $messageAry;
    }

}