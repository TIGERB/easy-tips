###### client和nginx简易交互过程
- step1:client发起http请求
- step2:dns服务器解析域名得到主机ip
- step3:默认端口为80，通过ip+port建立tcp/ip链接
- step4:建立连接的tcp/ip三次握手，建立成功发送数据包
- step5:nginx匹配请求
  + case .html: 静态内容，分发静态内容响应
  + case .php: php脚本，转发请求内容到php-fpm进程，分发php-fpm返回的内容响应
- step6:断开连接的tcp/ip四次握手，断开连接

###### nginx和php简易交互过程
- 背景：web server和服务端语言交互依赖的是cgi(Common Gateway Interface)协议，由于cgi效率不高，
后期产生了fastcgi协议(一种常驻型的cgi协议),php-cgi实现了fastcgi，但是相比php-cgi,php-fpm提供了
更好的PHP进程管理方式，可以有效控制内存和进程、可以平滑重载PHP配置
- 流程：
  + step1:nginx接收到一条http请求，会把环境变量，请求参数转变成php能懂的php变量
  ```
  // nginx 配置资料
  location ~ \.php$ {
        include snippets/fastcgi-php.conf; //step1
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;
  }
  ```
  + step2:nginx匹配到.php结尾的访问通过fastcgi_pass命令传递给php-fpm.sock文件，其实这里
的ngnix发挥的是反向代理的角色，把http协议请求转到fastcgi协议请求
  ```
  // nginx 配置资料
  location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/run/php/php7.0-fpm.sock;// step2
  }
  ```
  + step3:php-fpm.sock文件会被php-fpm的master进程所引用，这里nginx和php-fpm使用的是
  linux的进程间通信方式unix domain socks，是一种基于文件而不是网络底册协议的通信方式
  + step4:php-fpm的master进程接收到请求后，会把请求分发到php-fpm的子进程，每个php-fpm
子进程都包含一个php解析器
  + step5:php-fpm进程处理完请求后返回给nginx
