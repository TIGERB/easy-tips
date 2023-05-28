package main

import (
	// 导入net/http包
	"context"
	"http-demo/demov1"
	"io"
	"net/http"

	"google.golang.org/grpc"

	grpc_opentracing "github.com/grpc-ecosystem/go-grpc-middleware/tracing/opentracing"
	opentracing "github.com/opentracing/opentracing-go"
	jaeger "github.com/uber/jaeger-client-go"
	"github.com/uber/jaeger-client-go/transport"
)

var (
	// 创建一个tracer对象
	tracer opentracing.Tracer
)

func main() {
	// 指定上报数据的jaeger服务地址
	sender := transport.NewHTTPTransport(
		"http://go-jaeger-jaeger-demo:14268/api/traces",
	)
	var closer io.Closer
	tracer, closer = jaeger.NewTracer(
		"http-demo",
		jaeger.NewConstSampler(true),
		jaeger.NewRemoteReporter(sender),
	)
	defer closer.Close()

	http.HandleFunc("/v1/demo", func(w http.ResponseWriter, r *http.Request) {
		// 创建一个`span`
		span := tracer.StartSpan("demo_span_1")
		defer span.Finish()
		name, err := demoGrpcReq()
		if err != nil {
			w.Write([]byte(err.Error()))
		}
		// 写入响应内容
		w.Write([]byte(name))
	})

	// 启动一个http服务并监听6060端口 这里第二个参数可以指定handler
	http.ListenAndServe(":6060", nil)
}

func demoGrpcReq() (string, error) {
	// 使用opentracing中间件SDK go-grpc-middleware/tracing/opentracing
	conn, err := grpc.Dial("grpc-demo:1010", grpc.WithInsecure(), grpc.WithUnaryInterceptor(grpc_opentracing.UnaryClientInterceptor(
		grpc_opentracing.WithTracer(tracer),
	)))
	if err != nil {
		return "", err
	}
	defer conn.Close()

	client := demov1.NewGreeterClient(conn)
	resp, err := client.SayHello(context.TODO(), &demov1.HelloRequest{
		Name: "http request",
	})
	if err != nil {
		return "", err
	}
	return resp.GetMessage(), nil
}
