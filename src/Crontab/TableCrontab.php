<?php declare(strict_types = 1);

namespace Swoft\CronTask\Crontab;

class TableCrontab
{
    /**
     * @const 内存表大小
     */
    const TABLE_SIZE = 1024;

    /**
     * @var TableCrontab $instance 实例对象
     */
    private static $instance = null;

    /**
     * @var int $taskCount 最大任务数
     */
    public static $taskCount = 1024;

    /**
     * @var int $taskQueue 最大队列数
     */
    public static $taskQueue = 1024;

    /**
     * @var \Swoft\CronTask\Crontab\Table $originTable 内存任务表
     */
    private $originTable;

    /**
     * @var \Swoft\CronTask\Crontab\Table $runTimeTable 内存运行表
     */
    private $runTimeTable;

    /**
     * @var array $originStruct 任务表结构
     */
    private $originStruct = [
        'rule'       => [\Swoole\Table::TYPE_STRING, 100],
        'task' => [\Swoole\Table::TYPE_STRING, 255],
        'taskMethod' => [\Swoole\Table::TYPE_STRING, 255],
        'add_time'   => [\Swoole\Table::TYPE_STRING, 11],
    ];

    /**
     * @var array $runTimeStruct 运行表结构
     */
    private $runTimeStruct = [
        'task' => [\Swoole\Table::TYPE_STRING, 255],
        'taskMethod' => [\Swoole\Table::TYPE_STRING, 255],
        'minute'      => [\Swoole\Table::TYPE_STRING, 20],
        'sec'        => [\Swoole\Table::TYPE_STRING, 20],
        'runStatus'  => [\Swoole\Table::TYPE_INT, 4],
    ];

    /**
     * 创建配置表
     *
     * @param int $taskCount 最大任务数
     * @param int $taskQueue 最大队列数
     */
    public static function init(int $taskCount = null, int $taskQueue = null)
    {
        self::$taskCount = $taskCount == null ? self::$taskCount : $taskCount;
        self::$taskQueue = $taskQueue == null ? self::$taskQueue : $taskQueue;
        self::getInstance();
        self::$instance->initTables();
    }

    /**
     * 获取实例对象
     */
    public static function getInstance()
    {
        if (!(self::$instance instanceof self)) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    /**
     * 设置内存任务表实例
     *
     * @param Table $table 内存表
     */
    public function setOriginTable(Table $table)
    {
        $this->originTable = $table;
    }

    /**
     * 获取内存任务表实例
     */
    public function getOriginTable()
    {
        return $this->originTable;
    }

    /**
     * 设置执行任务表实例
     *
     * @param Table $table 执行任务表
     */
    public function setRunTimeTable(Table $table)
    {
        $this->runTimeTable = $table;
    }

    /**
     * 获取执行任务表实例
     *
     * @return Table
     */
    public function getRunTimeTable()
    {
        return $this->runTimeTable;
    }

    /**
     * 初始化任务表
     */
    private function initTables()
    {
        return $this->createOriginTable() && $this->createRunTimeTable();
    }

    /**
     * 创建originTable
     *
     * @return bool
     */
    private function createOriginTable(): bool
    {
        $this->setOriginTable(new Table('origin', self::TABLE_SIZE, $this->originStruct));

        return $this->getOriginTable()->create();
    }

    /**
     * 创建runTimeTable
     *
     * @return bool
     */
    private function createRunTimeTable(): bool
    {
        $this->setRunTimeTable(new Table('runTime', self::TABLE_SIZE, $this->runTimeStruct));

        return $this->getRunTimeTable()->create();
    }
}
