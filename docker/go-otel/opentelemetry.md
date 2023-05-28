# 一探究竟新一代可观测标准OpenTelemetry

## 什么是可观测

相信大家日常经常使用`kibana`、`grafana`、`jaeger`等平台观察系统状态和定位问题，这些就是可观测体系的一部分。可观测主要包括：

- 日志，实现有`ELK(es/logstash/kibana)`等
- 指标，实现有`grafana`+`promthues`等
- 追踪，基于`opentracing`协议的实现有`jaeger`、`skywalking`等

我们排查问题过程，一般都会把三者日志、指标、追踪结合来看，比如通过接口异常增量指标发现问题--->链路追踪定位异常服务--->排查异常服务日志，所以关于可观测我们经常可以看见这个经典的图片：

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/infra%2Ftelemetry.png" style="width:80%">
</p>

## 什么是`OpenTelemetry`

关于可观测除了上述的各种实现外，还有另一套实现`OpenCensus`，于是诞生统一标准`OpenTelemetry`且兼容`OpenTracing`，`OpenCensus`。不过关于go语言`OpenTelemetry`的统一sdk实现还不完善，比如目前还不支持日志，具体可以查看`https://github.com/open-telemetry/opentelemetry-go`。

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/infra%2Fopentelemtry.png" style="width:80%">
</p>

接下来，我们基于Go来看看，原sdk(也就是未使用`OpenTelemetry`)接入指标和追踪的方式和基于`OpenTelemetry`新体系的指标和追踪接入方式区别。

## 可观测之指标

### 基于原生`promethues`sdk的指标采集演示

> Go版本 这里用的1.14

主要依赖的包:
```
"github.com/prometheus/client_golang/prometheus"
"github.com/prometheus/client_golang/prometheus/promhttp"
```

就依赖了两个包，使用比较简单：
1. 单独创建一个server使用`github.com/prometheus/client_golang/prometheus/promhttp`暴露指标
2. 使用`github.com/prometheus/client_golang/prometheus`创建自定义指标，比如`NewCounterVec`创建计数器、`HistogramVec`创建直方图等等。
3. `WithLabelValues`给自定义指标打标签

代码示例如下：

> docker示例：https://github.com/TIGERB/easy-tips/tree/master/docker/grafana-promethues/go-demo/main.go.promethues

```go
package main

import (
	"net/http"

	// 引入prometheus sdk
	"github.com/prometheus/client_golang/prometheus"
	"github.com/prometheus/client_golang/prometheus/promhttp"
)

var (
	// 自定义接口请求次数自定义指标
	GlobalApiCounter *prometheus.CounterVec
)

func init() {
	// 初始化接口请求次数自定义指标
	GlobalApiCounter = prometheus.NewCounterVec(prometheus.CounterOpts{
		Name: "demo_api_request_counter",
		Help: "接口请求次数自定义指标",
	},
		[]string{"domain", "uri"}, // 域名和接口
	)
	prometheus.MustRegister(GlobalApiCounter)
}

func main() {
	go (func() {
		// 创建一个独立的server export暴露Go指标 避免通过业务服务暴露到外网
		metricServer := http.NewServeMux()
		metricServer.Handle("/metrics", promhttp.Handler())
		http.ListenAndServe(":2112", metricServer)
	})()

	// 使用默认server
	http.HandleFunc("/v1/demo", func(w http.ResponseWriter, r *http.Request) {
		// 自定义计数
		GlobalApiCounter.WithLabelValues(r.Host, r.RequestURI).Inc()

		w.Write([]byte("test"))
	})
	// 启动一个http服务并监听6060端口 这里第二个参数可以指定handler
	http.ListenAndServe(":6060", nil)
}
```

访问接口`curl 127.0.0.1:6060/v1/demo`后，查看指标输出 `curl 127.0.0.1:2112/metrics`，如下：

```
# HELP demo_api_request_counter 接口请求次数自定义指标
# TYPE demo_api_request_counter counter
demo_api_request_counter{doamin="127.0.0.1:6060",uri="/v1/demo"} 4
```

### 基于`OpenTelemetry`sdk的`promethues`指标采集演示

> Go版本 这里用的1.19 版本太低报错

主要依赖的包:
```
"github.com/prometheus/client_golang/prometheus/promhttp"
"go.opentelemetry.io/otel/attribute"
"go.opentelemetry.io/otel/exporters/prometheus"
"go.opentelemetry.io/otel/metric"
metricsdk "go.opentelemetry.io/otel/sdk/metric"
```

相对于原生prome只使用两个包，引入的包多几个：
1. 相同点：**仍然**单独创建一个server使用`github.com/prometheus/client_golang/prometheus/promhttp`暴露指标
2. 不同点：使用`go.opentelemetry.io/otel/exporters/prometheus`初始化一个指标对象`meter`
3. 不同点：使用`meter.Int64Counter`初始化计数器、直方图等
4. 不同点：`metric.WithAttributes`打标签


代码示例如下：

> docker示例：https://github.com/TIGERB/easy-tips/tree/master/docker/grafana-promethues/go-demo/main.go

```go
package main

import (
	"context"
	"net/http"

	// 引入prometheus sdk
	"github.com/prometheus/client_golang/prometheus/promhttp"
	"go.opentelemetry.io/otel/attribute"
	"go.opentelemetry.io/otel/exporters/prometheus"
	"go.opentelemetry.io/otel/metric"
	metricsdk "go.opentelemetry.io/otel/sdk/metric"
)

var meter metric.Meter

func init() {
	// 初始化指标meter
	mexp, err := prometheus.New()
	if err != nil {
		panic(err)
	}
	meter = metricsdk.NewMeterProvider(metricsdk.WithReader(mexp)).Meter("http-demo")
}

func main() {
	// 集成指标
	// https://github.com/open-telemetry/opentelemetry-go/blob/main/example/prometheus/main.go
	// 创建一个接口访问计数器
	urlCouter, _ := meter.Int64Counter("demo_api_request_counter", metric.WithDescription("QPS"))

	go (func() {
		// 创建一个独立的server export暴露Go指标 避免通过业务服务暴露到外网
		metricServer := http.NewServeMux()
		metricServer.Handle("/metrics", promhttp.Handler())
		http.ListenAndServe(":2112", metricServer)
	})()

	// 使用默认server
	http.HandleFunc("/v1/demo", func(w http.ResponseWriter, r *http.Request) {
		// 自定义计数
		opt := metric.WithAttributes(attribute.Key("domain").String(r.Host), attribute.Key("uri").String(r.RequestURI))
		urlCouter.Add(context.Background(), 1, opt) // 计数

		w.Write([]byte("test"))
	})
	// 启动一个http服务并监听6060端口 这里第二个参数可以指定handler
	http.ListenAndServe(":6060", nil)
}
```

访问接口`curl 127.0.0.1:6060/v1/demo`后，查看指标输出 `curl 127.0.0.1:2112/metrics`，如下：

```
# HELP demo_api_request_counter_total QPS
# TYPE demo_api_request_counter_total counter
demo_api_request_counter_total{domain="127.0.0.1:6060",otel_scope_name="http-demo",otel_scope_version="",uri="/v1/demo"} 1
```

## 可观测之追踪

### 基于`OpenTracing`sdk的`jaeger`追踪演示

> Go版本 这里用的1.18

主要依赖的包:
```
grpc_opentracing "github.com/grpc-ecosystem/go-grpc-middleware/tracing/opentracing"
opentracing "github.com/opentracing/opentracing-go"
jaeger "github.com/uber/jaeger-client-go"
"github.com/uber/jaeger-client-go/transport"
```

使用方式：
1. 使用`jaeger.NewTracer`创建一个tracer对象`tracer`，并制定jaeger服务地址(这里采用的是非agent方式演示)
2. 使用`tracer`创建一个span`tracer.StartSpan`，并在你想追踪的代码段后面`span.Finish()`
3. 这里上游使用`grpc`服务做测试，使用opentracing中间件SDK `go-grpc-middleware/tracing/opentracing`

> docker示例：https://github.com/TIGERB/easy-tips/tree/master/docker/go-jaeger

```go
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
```

使用`docker-compose up -d`启动演示服务，模拟请求`curl 127.0.0.1:6060/v1/demo`，查看jaeger后台`http://localhost:16686/`，示例如下：

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/infra%2Fjaeger.png" style="width:80%">
</p>

### 基于`OpenTelemetry`sdk的`jaeger`追踪演示

> Go版本 这里用的1.19 版本太低报错

主要依赖的包:
```
elemetry.io/contrib/instrumentation/net/http/otelhttp"
"google.golang.org/grpc"

"go.opentelemetry.io/contrib/instrumentation/google.golang.org/grpc/otelgrpc"
"go.opentelemetry.io/otel"
"go.opentelemetry.io/otel/exporters/jaeger"
"go.opentelemetry.io/otel/propagation"
"go.opentelemetry.io/otel/sdk/resource"
tracesdk "go.opentelemetry.io/otel/sdk/trace"
semconv "go.opentelemet
```

使用方式：
1. 
2. 


> docker示例：https://github.com/TIGERB/easy-tips/tree/master/docker/go-otel

```go
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
	conn, err := grpc.Dial("grpc-demo:1010",
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
```

使用`docker-compose up -d`启动演示服务，模拟请求`curl 127.0.0.1:6060/v1/demo`，查看jaeger后台`http://localhost:16686/`，示例如下：

<p align="center">
  <img src="https://blog-1251019962.cos.ap-beijing.myqcloud.com/infra%2Fjaeger-otel.png" style="width:80%">
</p>