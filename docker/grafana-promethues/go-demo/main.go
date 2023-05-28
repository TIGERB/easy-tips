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
