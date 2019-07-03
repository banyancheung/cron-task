<?php declare(strict_types=1);

namespace Swoft\CronTask;

/**
 * Class TaskRegister
 * @since 2.0
 */
class TaskRegister
{
    /**
     * @var array
     */
    private static $tasks = [];

    /**
     * @param string $class
     * @param string $method
     * @param string $cron
     * @param string $description
     */
    public static function bindTask(string $class, string $method, string $cron, string $description): void
    {
        // Storage
        self::$tasks[] = [
            'cron' => $cron,
            'method' => $method,
            'className' => $class,
            'description' => $description
        ];
    }

    /**
     * @return array
     */
    public static function getTasks(): array
    {
        return self::$tasks;
    }
}
