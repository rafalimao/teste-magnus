<?php

class RedisConfig {
    private $redis;
    private $host;
    private $port;

    public function __construct() {
        $this->host = $_ENV['REDIS_HOST'] ?? 'redis';
        $this->port = $_ENV['REDIS_PORT'] ?? 6379;
    }

    public function getConnection() {
        if (!$this->redis) {
            $this->redis = new Redis();
            try {
                $this->redis->connect($this->host, $this->port);
            } catch (Exception $e) {
                throw new Exception("Redis connection error: " . $e->getMessage());
            }
        }
        return $this->redis;
    }

    public function rpop($key) {
        $redis = $this->getConnection();
        $value = $redis->rpop($key);
        return $value ? json_decode($value, true) : null;
    }

    public function llen($key) {
        $redis = $this->getConnection();
        return $redis->llen($key);
    }
}

