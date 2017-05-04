## 概念

Hypertext Transfer Protocol, 超文本传输(转移)协议，是客户端和服务端传输文本制定的协议。构建WWW的具体的三项技术如下：

WWW: world wide web, 万维网
    
    - HTML: Hypertext Markup Language, 超文本标记语言
    - HTTP: Hypertext Transfer Protocol, 超文本传输(转移)协议 (HTTP是TCP/IP的应用层协议)
    - URL: Uniform Resource Locator, 统一资源定位符号 

> URI: Uniform Resource Identitier, 统一资源标示符号，URL是URI的子集

## TCP/IP

```
    应用层(http/https/websocket/ftp...) => 定义：文本传输协议
      |
    传输层(tcp/udp) => 定义：端口
      |
    网络层(ip)　=> 定义：IP
      |
    链路层(mac&数据包) => 定义：数据包，MAC地址
      |
    实体层(光缆/电缆/交换机/路由/终端...) => 定义：物理
```

TCP/IP:

- 解释一：分别代表tcp协议和ip协议
- 解释二：如果按照网络五层架构，TCP/IP代表除了应用层其他层所有协议簇的统称

TCP/IP connect: TCP/IP的三次握手:
```
          标有syn的数据包
          ------------->
          标有syn/ack的数据包
  client  <-------------  server
          标有ack的数据包
          -------------->
```

TCP/IP finish: TCP/IP的四次握手:
```
                          fin
                    <-------------
                          ack
client(或server)    -------------> server(或client)
                          fin
                    ------------->
                          ack
                    <-------------
```

## HTTP 报文

HTTP 报文由三部分组成:
- Start Line
- Headers
- Entity Body

HTTP 报文分为两类:
- 请求报文
- 响应报文

### 请求报文Start Line

语法 : <方法> <请求URL> <版本>

#### HTTP Method

+ get: 获取资源，不携带http body,支持查询参数，大小2KB
+ post: 传输资源，http body, 大小默认8M，1000个input variable
+ put: 传输资源，http body，资源更新
+ delete: 删除资源,不携带http body
+ patch: 传输资源，http body，存在的资源局部更新
+ head: 获取http header,不携带http body
+ options: 获取支持的method,不携带http body
+ trace: 追踪，返回请求回环信息,不携带http body
+ connect: 建立隧道通信

### 响应报文Start Line

语法 : <方法> <状态码> <原因短语>

#### HTTP Status Code

+ 200: ok
+ 301: 永久重定向
+ 302: 临时重定向
+ 303: 临时重定向，要求用get请求资源
+ 304: not modified, 返回缓存，和重定向无关
+ 307: 临时重定向,严格不从post到get
+ 400: 参数错误
+ 401: 未通过http认证
+ 403: forbidden，未授权
+ 404: not found，不存在资源
+ 500: internet server error，代码错误
+ 502: bad gateway，fastcgi返回的内容web server不明白
+ 503: service unavailable，服务不可用
+ 504: gateway timeout，fastcgi响应超时

### HTTP Header Fields

常见通用头部

+ Cache-Control:
  - no-cache: 不缓存过期的缓存
  - no-store: 不缓存
+ Pragma: no-cache, 不使用缓存，http1.1前的历史字段
+ Connection: 
  - 控制不在转发给代理首部不字段
  - Keep-Alive/Close: 持久连接
+ Date: 创建http报文的日期 

常见请求头

+ Accept: 可以处理的媒体类型和优先级 
+ Host: 目标主机域名
+ Referer: 请求从哪发起的原始资源URI
+ User-Agent: 创建请求的用户代理名称
+ Cookie: cookie信息  

常见响应头

+ Location: 重定向地址
+ Server: 被请求的服务web server的信息
+ Set-Cookie: 要设置的cookie信息
  - NAME: 要设置的键值对
  - expires: cookie过期时间
  - path: 指定发送cookie的目录
  - domain: 指定发送cookie的域名
  - Secure: 指定之后只有https下才发送cookie
  - HostOnly: 指定之后javascript无法读取cookie
+ Keep-Alive: 

HTTP协议初期每次连接结束后都会断开TCP连接，之后HEADER的connection字段定义Keep-Alive（HTTP 1.1 默认　持久连接），代表如果连接双方如果没有一方主动断开都不会断开TCP连接，减少了每次建立HTTP连接时进行TCP连接的消耗。

## Cookie/Session

+ Cookie: 工作机制是用户识别和状态管理，服务端为了管理用户的状态会通过客户端，把一些临时的数据写入到设备中Set-Cookie，当用户访问服务的时候，服务可以通过通信的方式取回之前存放的cookie。
+ Session:　由于http是无状态的，请求之间无法维系上下文，所以就出现了session作为会话控制，服务端存放用户的会话信息。

## HTTPs

概念:在http协议上增加了ssl(secure socket layer)层。

```
    SSL层
      |
    应用层
      |
    传输层
      |
    网络层
      |
    链路层
      |
    实体层
```

HTTPS 认证流程
```

                              发起请求
                     --------------------------->　　server 
                              下发证书
                      <---------------------------   server 
                      证书数字签名(用证书机构公钥加密)
                     --------------------------->　　证书机构 
                          证书数字签名验证通过
client(内置证书机构证书) <---------------------------   证书机构
                      公钥加密随机密码串(未来的共享秘钥)
                     --------------------------->　　server私钥解密(非对称加密)
                        SSL协议结束　HTTP协议开始
                      <---------------------------   server(对称加密)
                            共享秘钥加密 HTTP
                     --------------------------->　　server(对称加密)
```

+ 核对证书证书： 证书机构的公开秘钥验证证书的数字签名
+ 公开密钥加密建立连接：非对称加密
+ 共享密钥加密

## Websocket

+ 基于http协议建立连接，header的upgrade字段转化协议为websocket
+ 全双工通信，客户端建立连接

## HTTP2

+ 多路复用：多个请求共享一个tcp连接
+ 全双工通信
+ 必须https://
+ 头部压缩
+ 二进制传输