<?php

// 创建一个socket(tcp/ip)
$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (! $server) {
    // exception
    throw new Exception('', 500);
}

// 绑定ip/port
if (! socket_bind($server, '127.0.0.1', '8889')) {
    // exception
    throw new Exception('', 500);
}

// 监听端口
socket_listen($server);

$shareMemoryId = null;
$shmopSize = 0;

// 创建共享内存
// $shamKey = ftok(__FILE__, 't');
// // var_dump($shamKey);
// // die;
// $shmopSize = 6; // byte

// $shareMemoryId = shmop_open($shamKey, 'c', 0777, $shmopSize);
// shmop_delete($shareMemoryId);

// if (! $shareMemoryId = shmop_open($shamKey, 'c', 0777, $shmopSize)) {
//     // exception
//     throw new Exception('', 500);
// }

// shmop_write($shareMemoryId, "000000", 0);
// var_dump(shmop_write($shareMemoryId, "11111111", 0));
// var_dump(shmop_read($shareMemoryId, 0, $shmopSize));
// die;

foreach (range(1, 2) as $v) {
    forkWorker($server, $shareMemoryId, $shmopSize);
}

// 主进程挂起
while (true) {

    echo pcntl_wait($status) . "\n";
    echo $status . "\n";
    // usleep(100000);
}

function forkWorker($server, $shareMemoryId, $shmopSize)
{
    $pid = pcntl_fork();

    if ($pid == 0) {
        $currentPid = posix_getpid();
        // $len = strlen($currentPid);
        // if ($len < 6) {
        //     foreach (range(1, 6-$len) as $v) {
        //         $currentPid .= '0';
        //     }
        // }
        while (true) {
            // 抢锁
            // usleep(100000);
            // $mutexPid = shmop_read($shareMemoryId, 0, $shmopSize);
            // if ($mutexPid === false) {
            //     continue;
            // }
            // if ($mutexPid !== '000000' && $mutexPid !== $currentPid) {
            //     continue;
            // }
            // $wRes = shmop_write($shareMemoryId, (string)$currentPid, 0);
            // if ($wRes === false) {
            //     continue;
            // }
            // $mutexPid = shmop_read($shareMemoryId, 0, $shmopSize);
            // if ($mutexPid !== $currentPid) {
            //     continue;
            // }
            // echo "{$currentPid} 抢锁成功" . "\n";

            // accept
            $client = socket_accept($server);
            if (! $client) {
                continue;
            }
            $request = '';
            $buf = '';
            // $i = 0;
            do {
                // $i++;
                $res = socket_recv($client, $buf, 1024, MSG_DONTWAIT);
                if ($buf) {
                    $request .= $buf;
                }
                // var_dump($currentPid, $i, $res);
            } while ($res && $res == 1024);

            $http = new HttpProtocol;
            $http->originRequestContentString = $request;
            $http->request($request);

            $filename = dirname(__DIR__) . '/index.html';
            $f = fopen($filename, 'r');
            $data = fread($f, filesize($filename));
            fclose($f);

            $http->response($data);
            socket_write($client, $http->responseData);
            socket_close($client);
            echo socket_strerror(socket_last_error($server)) . "\n";

            // shmop_write($shareMemoryId, "000000", 0);
            // echo "{$currentPid} 释放锁成功" . "\n";
        }
        return;
    } else {
        
    }
}

/**
 * php实现简单的http协议
 */
class HttpProtocol
{
    /**
     * 原始请求字符串
     *
     * @var string
     */
    public  $originRequestContentString = '';

    /**
     * 原始请求字符串拆得的列表
     *
     * @var array
     */
    private $originRequestContentList = [];

    /**
     * 原始请求字符串拆得的键值对
     *
     * @var array
     */
    private $originRequestContentMap = [];

    /**
     * 定义响应头信息
     *
     * @var array
     */
    private $responseHead = [
        'http'         => 'HTTP/1.1 {STATUS} {DESC}',
        'content-type' => 'Content-Type: text/html',
        'server'       => 'Server: php/0.0.1',
    ];

    /**
     * 定义响应体信息
     *
     * @var string
     */
    private $responseBody = '';

    /**
     * 响应内容
     *
     * @var string
     */
    public  $responseData = '';

    /**
     * 解析请求信息
     *
     * @param string $content
     * @return void
     */
    public function request($content = '')
    {
        if (empty($content)) {
            // exception
        
        }
        $this->originRequestContentList = explode("\r\n", $this->originRequestContentString);
        if (empty($this->originRequestContentList)) {
            // exception

        }
        foreach ($this->originRequestContentList as $k => $v) {
            if ($v === '') {
                // 过滤空
                continue;
            }
            if ($k === 0) {
                // 解析http method/request_uri/version
                list($http_method, $http_request_uri, $http_version) = explode(' ', $v);
                $this->originRequestContentMap['Method'] = $http_method;
                $this->originRequestContentMap['Request-Uri'] = $http_request_uri;
                $this->originRequestContentMap['Version'] = $http_version;
                continue;
            }
            list($key, $val) = explode(': ', $v);
            $this->originRequestContentMap[$key] = $val;
        }
    }
    
    /**
     * 组装响应内容
     *
     * @param [type] $responseBody
     * @return void
     */
    public function response($responseBody)
    {
        $count = count($this->responseHead);
        $this->responseHead['pid'] .= posix_getpid();
        $finalHead = '';
        foreach ($this->responseHead as $v) {
            $finalHead .= $v . "\r\n";
        }
        $this->responseData = $finalHead . "\r\n" . $responseBody;
        $this->trace();
    }

    public function process()
    {
        
    }

    public function trace()
    {
        echo json_encode([
            'request' => $this->originRequestContentMap,
            'response'=> $this->responseData
        ]) . "\n";
    }
}