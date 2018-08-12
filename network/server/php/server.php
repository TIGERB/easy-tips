<?php

// 创建一个socket(tcp/ip)
$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);

// 绑定ip/port
socket_bind($server, '127.0.0.1', '8889');

// 监听端口
socket_listen($server);

// 接受一个客户连接
while (true) {
    $client = socket_accept($server);
    if (! $client) {
        continue;
    }
    $request = '';
    $buffer = '';
    while (empty($buffer)) {
        $buffer = socket_read($client, 1024);
        if ($buffer) {
            $request .= $buffer;
        }
    }

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
}

// 实现http协议

class HttpProtocol
{
    public  $originRequestContentString = '';
    private $originRequestContentList = [];
    private $originRequestContentMap = [];
    private $responseHead = [
        'http'         => 'HTTP/1.1 200 OK',
        'content-type' => 'Content-Type: text/html',
        'server'       => 'Server: php/0.0.1'
    ];
    private $responseBody = '';
    public  $responseData = '';

    public function request($content = '')
    {
        if (empty($content)) {
            // 异常
        
        }
        $this->originRequestContentList = explode("\r\n", $this->originRequestContentString);
        if (empty($this->originRequestContentList)) {
            // 异常

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
    
    public function response($data)
    {
        $count = count($this->responseHead);
        $finalHead = '';
        foreach ($this->responseHead as $v) {
            $finalHead .= $v . "\r\n";
        }
        $this->responseData = $finalHead . "\r\n" . $data;
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