<?php

/**
 * 定时清理redis中超时以及即将超时的FormId，每天15:30和3:30清理一次
 */
    $redis = new Redis();
    $redis->connect('127.0.0.1','6379');
    $redis->auth('rootzjc');
    $redis->select(6);
    $time = time();
    $script = <<<SCRIPT
                local time = KEYS[1]
                local max = time - 604800 + 44800
                redis.call("Zremrangebyscore","formIdToB",0,max)
                redis.call("Zremrangebyscore","formIdToC",0,max)
SCRIPT;
    return $redis->eval($script,[$time],1);