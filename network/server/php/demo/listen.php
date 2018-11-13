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

while (true) {
    // accept
    $client = socket_accept($server);
    if (! $client) {
        continue;
    }
    $request = socket_read($client, 1024);

    $http = new HttpProtocol;
    $http->originRequestContentString = $request;
    $http->request($request);
    $http->response("Hello World");
    socket_write($client, $http->responseData);

    socket_close($client);
    echo socket_strerror(socket_last_error($server)) . "\n";
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
        'http'         => 'HTTP/1.1 200 OK',
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