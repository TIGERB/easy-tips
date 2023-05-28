package main

import (
	// 导入net/http包
	"context"
	"http-demo/demov1"
	"net/http"

	"go.opentelemetry.io/contrib/instrumentation/net/http/otelhttp"
	"google.golang.org/grpc"

	"go.opentelemetry.io/contrib/instrumentation/google.golang.org/grpc/otelgrpc"
	"go.opentelemetry.io/otel"
	"go.opentelemetry.io/otel/exporters/jaeger"
	"go.opentelemetry.io/otel/propagation"
	"go.opentelemetry.io/otel/sdk/resource"
	tracesdk "go.opentelemetry.io/otel/sdk/trace"
	semconv "go.opentelemetry.io/otel/semconv/v1.17.0"
)

// 追踪
var tracer *tracesdk.TracerProvider

func init() {
	// 初始化追踪tracer
	// https://github.com/open-telemetry/opentelemetry-go/blob/main/example/jaeger/main.go
	// Create the Jaeger exporter
	exp, err := jaeger.New(jaeger.WithCollectorEndpoint(jaeger.WithEndpoint("http://go-otel-jaeger:14268/api/traces")))
	if err != nil {
		panic(err)
		return
	}
	tracer = tracesdk.NewTracerProvider(
		tracesdk.WithBatcher(exp),
		tracesdk.WithResource(resource.NewWithAttributes(
			semconv.SchemaURL,
			semconv.ServiceNameKey.String("http-demo"),
		)),
	)

}

// 常见框架集成opentelemetry SDK
// https://github.com/open-telemetry/opentelemetry-go-contrib/tree/main/instrumentation
func main() {
	otel.SetTracerProvider(tracer)
	otel.SetTextMapPropagator(propagation.NewCompositeTextMapPropagator(propagation.TraceContext{}, propagation.Baggage{}))
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()
	defer tracer.Shutdown(ctx)

	// /v1/demo接口 业务逻辑handler
	demoHandler := func(w http.ResponseWriter, r *http.Request) {
		// 调用demo grpc接口
		name, err := demoGrpcReq()
		if err != nil {
			w.Write([]byte(err.Error()))
		}
		// 写入响应内容
		w.Write([]byte(name))
	}

	// 集成链路追踪
	// https://github.com/open-telemetry/opentelemetry-go-contrib/blob/main/instrumentation/net/http/otelhttp/example/server/server.go
	otelHandler := otelhttp.NewHandler(http.HandlerFunc(demoHandler), "otelhttp demo test")
	http.Handle("/v1/demo", otelHandler)

	// 启动一个http服务并监听6060端口 这里第二个参数可以指定handler
	http.ListenAndServe(":6060", nil)
}

func demoGrpcReq() (string, error) {
	// 使用grpc otel追踪sdk go.opentelemetry.io/contrib/instrumentation/google.golang.org/grpc/otelgrpc
	conn, err := grpc.Dial("go-otel-grpc-demo:1010",
		grpc.WithInsecure(),
		grpc.WithUnaryInterceptor(otelgrpc.UnaryClientInterceptor()),
		grpc.WithStreamInterceptor(otelgrpc.StreamClientInterceptor()),
	)
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
