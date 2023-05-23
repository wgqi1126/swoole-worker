<?php

namespace Wgqi1126\SwooleWorker;

use Swoole\Coroutine\Channel;

/**
 * 任务产生和消费实体
 */
interface SwooleJob
{
    /**
     * 任务产生
     * @param Channel $channel 任务数据推送管道
     */
    public function master(Channel $channel);

    /**
     * 任务执行
     * @param $data mixed 任务数据
     */
    public function worker($data = null);
}
