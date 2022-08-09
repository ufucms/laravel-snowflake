<?php

declare(strict_types=1);

namespace Ufucms\Snowflake;

class Snowflake
{
    protected const DEFAULT_EPOCH_DATETIME = '2022-08-08 08:08:08';

    protected const ID_BITS = 63;

    protected const TIMESTAMP_BITS = 41;

    protected const WORKER_ID_BITS = 5;

    protected const DATACENTER_ID_BITS = 5;

    protected const SEQUENCE_BITS = 12;

    protected const TIMEOUT = 1000;

    protected const MAX_SEQUENCE = 4095;

    /**
     * 开始时间.
     *
     * @var int
     */
    protected $epoch;

    /**
     * 最后的时间戳.
     *
     * @var int
     */
    private $lastTimestamp;

    /**
     * 序号.
     *
     * @var int
     */
    private $sequence = 0;

    /**
     * 数据中心 id.
     *
     * @var int
     */
    private $datacenterId;

    /**
     * 机器 id.
     *
     * @var int
     */
    private $workerId;

    /**
     * 创建一个新的雪花实例。
     */
    public function __construct(int $timestamp = null, int $workerId = 1, int $datacenterId = 1)
    {
        if ($timestamp === null) {
            $timestamp = strtotime(self::DEFAULT_EPOCH_DATETIME);
        }

        $this->epoch = $timestamp;
        $this->workerId = $workerId;
        $this->datacenterId = $datacenterId;
        $this->lastTimestamp = $this->epoch;
    }

    public function makeSequenceId(int $currentTime, int $max = self::MAX_SEQUENCE): int
    {
        if ($this->lastTimestamp === $currentTime) {
            $this->sequence = $this->sequence + 1;
            return $this->sequence;
        }

        $this->sequence = mt_rand(0, $max);
        $this->lastTimestamp = $currentTime;
        return $this->sequence;
    }

    /**
     * 创建无符号bigint 64bit 唯一Id 大约可用69年.
     * timestamp_bits(41) + datacenter_id_bits(5) + worker_id_bits(5) + sequence_bits(12)
     *
     * @return int
     */
    public function id(): int
    {
        $currentTime = $this->timestamp();
        while (($sequenceId = $this->makeSequenceId($currentTime)) > self::MAX_SEQUENCE) {
            usleep(1);
            $currentTime = $this->timestamp();
        }

        $this->lastTimestamp = $currentTime;
        return $this->toSnowflakeId($currentTime - $this->epoch * 1000, $sequenceId);
    }

    /**
     * 创建无符号bigint 64bit 唯一Id 大约可用69年.
     * timestamp_bits(41) + datacenter_id_bits(5) + worker_id_bits(5) + sequence_bits(12)
     *
     * @return int
     */
    public static function nextId(): int
    {
        return (new static)->id();
    }

    /**
     * 创建兼容JS 53bit 唯一Id 大约可用68年.
     * timestamp_bits(31) + datacenter_id_bits(5) + worker_id_bits(5) + sequence_bits(12)
     *
     * @return int
     */
    public function short(): int
    {
        $currentTime = $this->timestamp(false);
        while (($sequenceId = $this->makeSequenceId($currentTime)) > self::MAX_SEQUENCE) {
            usleep(1);
            $currentTime = $this->timestamp(false);
        }

        $this->lastTimestamp = $currentTime;
        return $this->toSnowflakeId($currentTime - $this->epoch, $sequenceId);
    }

    public function toSnowflakeId(int $currentTime, int $sequenceId)
    {
        $workerIdLeftShift = self::SEQUENCE_BITS;
        $datacenterIdLeftShift = self::WORKER_ID_BITS + self::SEQUENCE_BITS;
        $timestampLeftShift = self::DATACENTER_ID_BITS + self::WORKER_ID_BITS + self::SEQUENCE_BITS;

        return ($currentTime << $timestampLeftShift)
            | ($this->datacenterId << $datacenterIdLeftShift)
            | ($this->workerId << $workerIdLeftShift)
            | ($sequenceId);
    }

    /**
     * 返回现在的时间戳.
     *
     * @return int
     */
    public function timestamp($microtime=true): int
    {
        if($microtime){
            return (int) floor(microtime(true) * 1000);
        }else{
            return (int) time();
        }
    }

    public function parse(int $id, bool $is_short=false): array
    {
        $id = decbin($id);

        $datacenterIdLeftShift = self::WORKER_ID_BITS + self::SEQUENCE_BITS;
        $timestampLeftShift = self::DATACENTER_ID_BITS + self::WORKER_ID_BITS + self::SEQUENCE_BITS;

        $binaryTimestamp = substr($id, 0, -$timestampLeftShift);
        $binarySequence  = substr($id, -self::SEQUENCE_BITS);
        $binaryWorkerId  = substr($id, -$datacenterIdLeftShift, self::WORKER_ID_BITS);
        $binaryDatacenterId = substr($id, -$timestampLeftShift, self::DATACENTER_ID_BITS);
        if($is_short){
            $timestamp = (int) bindec($binaryTimestamp);
        }else{
            $timestamp = (int) (bindec($binaryTimestamp) / 1000);
        }
        $datetime  = date('Y-m-d H:i:s', ((int) ($timestamp + $this->epoch) | 0));

        return [
            'binary_length' => strlen($id),
            'binary' => $id,
            'binary_timestamp' => $binaryTimestamp,
            'binary_sequence' => $binarySequence,
            'binary_worker_id' => $binaryWorkerId,
            'binary_datacenter_id' => $binaryDatacenterId,
            'timestamp' => $timestamp,
            'sequence' => bindec($binarySequence),
            'worker_id' => bindec($binaryWorkerId),
            'datacenter_id' => bindec($binaryDatacenterId),
            'epoch' => $this->epoch,
            'datetime' => $datetime,
        ];
    }
}
