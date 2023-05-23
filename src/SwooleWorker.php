<?php

namespace Wgqi1126\SwooleWorker;

use Swoole\Coroutine\Barrier;
use Swoole\Coroutine\Channel;
use function Swoole\Coroutine\run;
use Swoole\Coroutine;

class SwooleWorker
{
    /**
     * @var Channel 用于传递任务数据
     */
    protected Channel $channel;
    /**
     * @var SwooleJob 任务生成和消费实现实例
     */
    protected SwooleJob $job;
    /**
     * @var int Worker 的数量，默认是 CPU 的核心数 * 2
     */
    protected int $workersN;

    /**
     * 创建任务
     * @param SwooleJob $job 任务生成和消费实现实例
     * @param int $workersN Worker 的数量，默认是 CPU 的核心数 * 2
     */
    public function __construct(SwooleJob $job, int $workersN = 0)
    {
        $this->job = $job;
        $this->workersN = $workersN === 0 ? (swoole_cpu_num() * 2) : $workersN;
        $this->channel = new Channel();
    }

    /**
     * 执行任务
     */
    public function run()
    {
        run(function () {
            $barrier = Barrier::make();

            Coroutine::create(function () use ($barrier) {
                $this->job->master($this->channel);
                $this->channel->close();
            });


            foreach (range(1, $this->workersN) as $_) {
                Coroutine::create(function () use ($barrier) {
                    while (true) {
                        $v = $this->channel->pop();
                        if ($v === false) break;
                        $this->job->worker($v);
                    }
                });
            }

            Barrier::wait($barrier);
        });
    }
}
