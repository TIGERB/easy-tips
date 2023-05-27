# 一探究竟新一代可观测标准OpenTelemetry

指标 Promethues

```
go get github.com/prometheus/client_golang/prometheus/promhttp
```

```Go
package main

import (
	"net/http"

	"github.com/prometheus/client_golang/prometheus/promhttp"
)

func main() {
	// 对外提供/metrics接口
	http.Handle("/metrics", promhttp.Handler())
	http.ListenAndServe(":2112", nil)
}
```

```
启动Go服务，curl请求接口：

curl http://localhost:2112/metrics
```

```
获取到监控指标数据如下：

# HELP go_gc_duration_seconds A summary of the pause duration of garbage collection cycles.
# TYPE go_gc_duration_seconds summary
go_gc_duration_seconds{quantile="0"} 2.8697e-05
go_gc_duration_seconds{quantile="0.25"} 3.8094e-05
go_gc_duration_seconds{quantile="0.5"} 0.000125819
go_gc_duration_seconds{quantile="0.75"} 0.000190862
go_gc_duration_seconds{quantile="1"} 0.0098972
go_gc_duration_seconds_sum 0.025042382
go_gc_duration_seconds_count 45
......略
```

追踪 OpenTracing

```Go
```


OpenTelemetry 指标/追踪

```Go
package main

import (
	// 导入net/http包
	"context"
	"http-demo/demov1"
	"net/http"

	"go.opentelemetry.io/otel/attribute"

	"go.opentelemetry.io/contrib/instrumentation/net/http/otelhttp"
	"google.golang.org/grpc"

	"go.opentelemetry.io/contrib/instrumentation/google.golang.org/grpc/otelgrpc"
	"go.opentelemetry.io/otel"
	"go.opentelemetry.io/otel/exporters/jaeger"
	"go.opentelemetry.io/otel/exporters/prometheus"
	"go.opentelemetry.io/otel/metric"
	"go.opentelemetry.io/otel/propagation"
	"go.opentelemetry.io/otel/sdk/resource"
	tracesdk "go.opentelemetry.io/otel/sdk/trace"
	semconv "go.opentelemetry.io/otel/semconv/v1.17.0"

	"github.com/prometheus/client_golang/prometheus/promhttp"
	metricsdk "go.opentelemetry.io/otel/sdk/metric"
)

var tracer *tracesdk.TracerProvider

var meter metric.Meter

func init() {
	// 初始化追踪tracer
	// https://github.com/open-telemetry/opentelemetry-go/blob/main/example/jaeger/main.go
	// Create the Jaeger exporter
	exp, err := jaeger.New(jaeger.WithCollectorEndpoint(jaeger.WithEndpoint("http://jaeger-demo:14268/api/traces")))
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

	// 初始化指标meter
	mexp, err := prometheus.New()
	if err != nil {
		panic(err)
	}
	meter = metricsdk.NewMeterProvider(metricsdk.WithReader(mexp)).Meter("http-demo")
}

// 常见框架集成opentelemetry SDK
// https://github.com/open-telemetry/opentelemetry-go-contrib/tree/main/instrumentation

func main() {
	otel.SetTracerProvider(tracer)
	otel.SetTextMapPropagator(propagation.NewCompositeTextMapPropagator(propagation.TraceContext{}, propagation.Baggage{}))
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()
	defer tracer.Shutdown(ctx)

	// 集成指标
	// https://github.com/open-telemetry/opentelemetry-go/blob/main/example/prometheus/main.go
	// 创建一个接口访问计数器
	urlCouter, _ := meter.Int64Counter("api_query_couter", metric.WithDescription("QPS"))

	// /v1/demo接口 业务逻辑handler
	demoHandler := func(w http.ResponseWriter, r *http.Request) {
		// 记录接口 QPS
		opt := metric.WithAttributes(attribute.Key("service_name").String("http-demo"), attribute.Key("url").String("/v1/demo"))
		urlCouter.Add(context.Background(), 1, opt) // 计数

		// 调用demo grpc接口
		name, err := demoGrpcReq()
		if err != nil {
			w.Write([]byte(err.Error()))
		}
		// 写入响应内容
		w.Write([]byte(name))
	}

	// 集成指标
	// https://github.com/open-telemetry/opentelemetry-go/blob/main/example/prometheus/main.go
	go (func() {
		httpmux := http.NewServeMux()
		httpmux.Handle("/metrics", promhttp.Handler())
		http.ListenAndServe(":6061", httpmux)
	})()

	// 集成链路追踪
	// https://github.com/open-telemetry/opentelemetry-go-contrib/blob/main/instrumentation/net/http/otelhttp/example/server/server.go
	otelHandler := otelhttp.NewHandler(http.HandlerFunc(demoHandler), "otelhttp demo test")
	http.Handle("/v1/demo", otelHandler)

	// 启动一个http服务并监听6060端口 这里第二个参数可以指定handler
	http.ListenAndServe(":6060", nil)
}

func demoGrpcReq() (string, error) {
	conn, err := grpc.Dial("grpc-demo:1010",
		grpc.WithInsecure(),
		grpc.WithUnaryInterceptor(otelgrpc.UnaryClientInterceptor()),
		grpc.WithStreamInterceptor(otelgrpc.StreamClientInterceptor()),
	)
	if err != nil {
		return "", err
	}
	// 泄露
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


```