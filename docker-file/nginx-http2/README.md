# http2

```
docker run -it -p 80:80 -p 443:443 --name nginx-http2 nginx:1.15-alpine sh
```

```
apk update && apk add --no-cache openssl

openssl genrsa -des3 -out server.key 2048

openssl rsa -in server.key -out server.key

openssl req -new -key server.key -out server.csr

openssl req -new -x509 -key server.key -out ca.crt -days 3650

openssl x509 -req -days 3650 -in server.csr -CA ca.crt -CAkey server.key -CAcreateserial -out server.crt
```

```
server {
    listen       443 ssl;

    server_name  http2.tigeb.cn;
    ssl_certificate     /etc/nginx/conf.d/tigerb.cn_bundle.crt;
    ssl_certificate_key  /etc/nginx/conf.d/tigerb.cn.key;
    #ssl_client_certificate ca.crt;
    #ssl_verify_client on;
    ssl_session_timeout  5m;
    ssl_protocols  SSLv2 SSLv3 TLSv1;
    ssl_ciphers  ALL:!ADH:!EXPORT56:RC4+RSA:+HIGH:+MEDIUM:+LOW:+SSLv2:+EXP;
    ssl_prefer_server_ciphers   on;

    #charset koi8-r;
    access_log  /var/log/nginx/host.access.log  main;

    location / {
        root   /usr/share/nginx/html;
        index  index.html index.htm;
    }

    #error_page  404              /404.html;
}
```
