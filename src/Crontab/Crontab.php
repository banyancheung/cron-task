<?php declare(strict_types=1);

namespace Swoft\CronTask\Crontab;

use Swoft\App;
use Swoft\Bean\Annotation\Mapping\Bean;
use Swoft\CronTask\PipeMessage;
use Swoft\CronTask\TaskRegister;
use Swoole\Table as SwooleTable;

/**
 * crontab
 */
class Crontab
{

    private static $instance = null;

    /**
     * @const 任务未执行态
     */
    const NORMAL = 0;

    /**
     * @const 任务完成态
     */
    const FINISH = 1;

    /**
     * @const 任务运行态
     */
    const START = 2;

    /**
     * @var array $task corntab任务
     */
    private $task;

    /**
     * 创建配置表
     */
    public static function init(): void
    {
        self::getInstance();
        self::$instance->initTasks();
        self::$instance->initLoad();
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
     * 初始化Tasks任务
     */
    public function initTasks()
    {
        $tasks = TaskRegister::getTasks();
        $this->setTasks($tasks);
    }

    /**
     * 初始化数据表
     *
     * @return bool
     */
    public function initLoad(): bool
    {
        $tasks = $this->getTasks();
        if (\count($tasks) <= 0) {
            return false;
        }
        foreach ($tasks as $tasksIndex => $task) {
            $this->checkTaskCount();
            $time = time();
            $key = $this->getKey($task['cron'], $task['task'], $task['method']);
            // 防止重复写入任务
            if (!$this->getOriginTable()->exist($key)) {
                $this->getOriginTable()->set($key, [
                    'rule' => $task['cron'],
                    'task' => $task['task'],
                    'taskMethod' => $task['method'],
                    'add_time' => $time
                ]);
            }
        }
        return true;
    }

    /**
     * 检测crontab任务数量
     *
     * @return bool
     * @throws \InvalidArgumentException
     */
    private function checkTaskCount(): bool
    {
        if (!isset($i)) {
            static $i = 0;
        }

        $i++;
        if ($i > TableCrontab::$taskCount) {
            throw new \InvalidArgumentException('The crontab task::' . $i . ' exceeds the threshold::' . TableCrontab::$taskCount);
        }

        return true;
    }

    /**
     * 更新要执行的task
     */
    public function checkTask(): void
    {
        $this->cleanRunTimeTable();
        $this->loadTableTask();
    }

    /**
     * 清理执行任务表
     */
    private function cleanRunTimeTable()
    {
        $currentTime = time();
        foreach ($this->getRunTimeTable()->getTable() as $key => $value) {
            if ($value['runStatus'] === self::FINISH || $value['sec'] < $currentTime) {
                $this->getRunTimeTable()->del($key);
            }
        }
    }

    /**
     * 获取配置任务列表
     *
     * @return array
     */
    public function getTasks(): array
    {
        return $this->task;
    }

    /**
     * 设置crontab任务配置
     *
     * @param array $tasks 任务配置
     * @return array
     */
    public function setTasks(array $tasks)
    {
        $this->task = $tasks;
    }

    /**
     * 获取原始数据表
     *
     * @return Table
     */
    public function getOriginTable(): Table
    {
        return TableCrontab::getInstance()->getOriginTable();
    }

    /**
     * 运行的数据表
     *
     * @return Table
     */
    public function getRunTimeTable(): Table
    {
        return TableCrontab::getInstance()->getRunTimeTable();
    }

    /**
     * 获取key值
     *
     * @param string $rule 规则
     * @param string $task 任务名
     * @param string $taskMethod 任务方法
     * @param string $min 分
     * @param string $sec 时间戳
     * @return string
     */
    public function getKey(string $rule, string $task, string $taskMethod, $min = '', $sec = ''): string
    {
        return md5($rule . $task . $taskMethod . $min . $sec);
    }

    /**
     * 获取内存中的任务信息
     */
    public function loadTableTask()
    {
        $originTableTasks = $this->getOriginTable()->getTable();
        if (\count($originTableTasks) > 0) {
            $time = time();
            $this->checkTaskQueue(true);
            foreach ($originTableTasks as $id => $task) {
                $parseResult = ParseCrontab::parse($task['rule'], $time);
                if ($parseResult === false) {
                    throw new \InvalidArgumentException(ParseCrontab::$error);
                } elseif (!empty($parseResult) && \is_array($parseResult)) {
                    $this->initRunTimeTableData($task, $parseResult);
                }
            }
        }
    }

    /**
     * 初始化runTimeTable数据
     *
     * @param array $task 任务
     * @param array $parseResult 解析crontab命令规则结果
     * @return bool
     */
    private function initRunTimeTableData(array $task, array $parseResult): bool
    {
        $runTimeTable = $this->getRunTimeTable();
        $min = date('YmdHi');
        $sec = strtotime(date('Y-m-d H:i'));

        foreach ($parseResult as $time) {
            $this->checkTaskQueue(false);
            $key = $this->getKey($task['rule'], $task['task'], $task['taskMethod'], $min, $time + $sec);
            $runTimeTable->set($key, [
                'task' => $task['task'],
                'taskMethod' => $task['taskMethod'],
                'minute' => $min,
                'sec' => $time + $sec,
                'runStatus' => self::NORMAL
            ]);
        }

        return true;
    }

    /**
     * 检测crontab队列数量
     *
     * @param bool $reStart 是否重新开始检测
     * @return bool
     */
    private function checkTaskQueue(bool $reStart): bool
    {
        if (!isset($i)) {
            static $i = 0;
        }

        if ($reStart) {
            $i = 0;
        }

        $i++;

        if ($i > TableCrontab::$taskQueue) {
            throw new \InvalidArgumentException('The crontab task-queue::' . $i . ' exceeds the threshold::' . TableCrontab::$taskQueue);
        }

        return true;
    }

    /**
     * 获取要执行的任务
     *
     * @return array
     */
    public function getExecTasks(): array
    {
        $data = [];
        $runTimeTableTasks = $this->getRunTimeTable()->getTable();
        if (\count($runTimeTableTasks) <= 0) {
            return $data;
        }
        $min = date('YmdHi');

        foreach ($runTimeTableTasks as $key => $value) {
            if ($value['minute'] == $min) {
                if (time() == $value['sec'] && $value['runStatus'] == self::NORMAL) {
                    $data[] = [
                        'key' => $key,
                        'task' => $value['task'],
                        'taskMethod' => $value['taskMethod'],
                    ];
                }
            }
        }

        foreach ($data as $item) {
            $this->startTask($item['key']);
        }

        return $data;
    }

    /**
     * 开始任务
     *
     * @param int $key 主键
     * @return bool
     */
    public function startTask($key)
    {
        return $this->getRunTimeTable()->set($key, ['runStatus' => self::START]);
    }

    /**
     * @param string $task
     * @param string $method
     */
    public function deliverTask(string $task, string $method): void
    {
        /* @var PipeMessageInterface $pipeMessage */
        $message = PipeMessage::pack(PipeMessage::MESSAGE_TYPE_TASK, [
            'name' => $task,
            'method' => $method
        ]);
        \server()->getSwooleServer()->sendMessage($message, 0);
    }

    /**
     * 完成任务
     *
     * @param int $key 主键
     * @return bool
     */
    public function finishTask($key)
    {
        return $this->getRunTimeTable()->set($key, ['runStatus' => self::FINISH]);
    }

}
