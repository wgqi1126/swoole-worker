# swoole-worker

Swoole 协程并发执行器

用法

```php
<?php

use Wgqi1126\SwooleWorker\SwooleJob;
use Swoole\Coroutine\Channel;
use Wgqi1126\SwooleWorker\SwooleWorker;

require_once __DIR__ . '/vendor/autoload.php';

$job = new class implements SwooleJob {
    public function master(Channel $channel)
    {
        print "master\n";
        for ($i = 0; $i < 100; $i++) $channel->push($i);
    }

    public function worker($data = null)
    {
        print "worker-{$data}\n";
        sleep(1);
    }
};
$worker = new SwooleWorker($job);
$worker->run();
```
