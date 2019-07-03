<?php declare(strict_types=1);

namespace Swoft\CronTask\Contract;

use Swoole\Table;

/**
 * Table interface
 */
interface TableInterface
{

    /**
     * 一个单位长度的int类型
     */
    const ONE_INT_LENGTH = 1;

    /**
     * 两个单位长度的int类型
     */
    const TWO_INT_LENGTH = 2;

    /**
     * 四个单位长度的int类型
     */
    const FOUR_INT_LENGTH = 4;

    /**
     * 八个单位长度的int类型
     */
    const EIGHT_INT_LENGTH = 8;

    /**
     * int类型
     */
    const TYPE_INT = Table::TYPE_INT;

    /**
     * string类型
     */
    const TYPE_STRING = Table::TYPE_STRING;

    /**
     * float类型
     */
    const TYPE_FLOAT = Table::TYPE_FLOAT;

    /**
     * 内存表增加一列
     *
     * @param string $name 列名
     * @param int $type 类型
     * @param int $size 最大长度，单位为字节
     */
    public function column(string $name, int $type, int $size = 0);

    /**
     * 创建内存表
     */
    public function create();

    /**
     * 设置行数据
     *
     * @param string $key 索引键
     * @param array $array 数据
     */
    public function set(string $key, array $array);

    /**
     * 原子自增操作
     *
     * @param string $key 索引键
     * @param string $column 列名
     * @param int|float $incrby 增量。如果列为整形，$incrby必须为int型，如果列为浮点型，$incrby必须为float类型
     */
    public function incr(string $key, string $column, $incrby = 1);

    /**
     * 原子自减操作
     *
     * @param string $key 索引键
     * @param string $column 列名
     * @param int|float $incrby 增量。如果列为整形，$incrby必须为int型，如果列为浮点型，$incrby必须为float类型
     */
    public function decr(string $key, string $column, $incrby = 1);

    /**
     * 获取一行数据
     *
     * @param string $key 索引键
     * @param string $field 列名
     */
    public function get(string $key, $field = null);

    /**
     * 检查table中是否存在某一个key
     *
     * @param string $key 索引键
     */
    public function exist(string $key);

    /**
     * 删除数据
     *
     * @param string $key 索引键
     */
    public function del(string $key);
}
