package main

import (
	"context"
	"flag"
	"grpc-demo/demo"
	"net"
	"net/http"

	"google.golang.org/grpc"

	grpcprometheus "github.com/grpc-ecosystem/go-grpc-prometheus"
	"github.com/prometheus/client_golang/prometheus/promhttp"

	"go.opentelemetry.io/contrib/instrumentation/google.golang.org/grpc/otelgrpc"
	"go.opentelemetry.io/otel"
	"go.opentelemetry.io/otel/exporters/jaeger"
	"go.opentelemetry.io/otel/propagation"
	"go.opentelemetry.io/otel/sdk/resource"
	tracesdk "go.opentelemetry.io/otel/sdk/trace"
	semconv "go.opentelemetry.io/otel/semconv/v1.17.0"
)

// https://github.com/grpc/grpc-go/tree/master/examples/helloworld

// protoc \
//     --go_out=plugins=grpc:./ \
//     ./demo/demo.proto

// https://github.com/grpc-ecosystem/grpc-gateway

// http proto demo https://github.com/googleapis/googleapis/blob/master/google/api/http.proto#L46

// protoc \
//     -I ./demo \
//     -I /Users/tigerb/Documents/code/work/code/golang-mod/pkg/mod/github.com/grpc-ecosystem/grpc-gateway@v1.16.0/third_party/googleapis \
//     --go_out=plugins=grpc:./ \
//     --grpc-gateway_out=logtostderr=true:. \
//     ./demo/demo.proto

// protoc \
//     -I ./demo \
//     -I /Users/tigerb/Documents/code/work/code/golang-mod/pkg/mod/github.com/grpc-ecosystem/grpc-gateway@v1.16.0/third_party/googleapis \
//     --go_out=plugins=grpc:./ \
// 	   --php_out=plugins=grpc:./ \
//     --grpc-gateway_out=logtostderr=true:. \
//	   --plugin=protoc-gen-grpc=bins/opt/grpc_php_plugin \
//     ./demo/demo.proto

var (
	grpcServerEndpoint = flag.String("grpc-server-endpoint", "localhost:1010", "gRPC server endpoint")

	tracer *tracesdk.TracerProvider
)

func init() {
	// 集成链路追踪
	// https://github.com/open-telemetry/opentelemetry-go/blob/main/example/jaeger/main.go
	// Create the Jaeger exporter
	exp, err := jaeger.New(jaeger.WithCollectorEndpoint(jaeger.WithEndpoint("http://jaeger-demo:14268/api/traces")))
	if err != nil {
		panic(err)
		return
	}
	tracer = tracesdk.NewTracerProvider(
		// Always be sure to batch in production.
		tracesdk.WithBatcher(exp),
		// Record information about this application in a Resource.
		tracesdk.WithResource(resource.NewWithAttributes(
			semconv.SchemaURL,
			semconv.ServiceNameKey.String("grpc-demo"),
			// attribute.String("environment", "production"),
			// attribute.Int64("ID", 2),
		)),
	)
	return
}

type demoServer struct {
}

func (s *demoServer) SayHello(context.Context, *demo.HelloRequest) (reply *demo.HelloReply, err error) {
	reply = &demo.HelloReply{
		Message: "hello test",
	}
	return
}

func main() {
	otel.SetTracerProvider(tracer)
	otel.SetTextMapPropagator(propagation.NewCompositeTextMapPropagator(propagation.TraceContext{}, propagation.Baggage{}))
	ctx, cancel := context.WithCancel(context.Background())
	defer cancel()
	defer tracer.Shutdown(ctx)

	go runGrpc()
	runHttp()
}

func runGrpc() {
	listener, err := net.Listen("tcp", ":1010")
	if err != nil {
		panic(err)
	}
	s := grpc.NewServer(
		// grpc.StreamInterceptor(grpcprometheus.StreamServerInterceptor),
		// grpc.UnaryInterceptor(grpcprometheus.UnaryServerInterceptor),
		// 集成opentelemetry
		grpc.UnaryInterceptor(otelgrpc.UnaryServerInterceptor()),
		grpc.StreamInterceptor(otelgrpc.StreamServerInterceptor()),
	)
	demo.RegisterGreeterServer(s, &demoServer{})
	grpcprometheus.Register(s)
	err = s.Serve(listener)
	if err != nil {
		panic(err)
	}
}

func runHttp() {
	// ctx, cancel := context.WithCancel(context.Background())
	// defer cancel()

	// mux := runtime.NewServeMux()
	// opts := []grpc.DialOption{
	// 	grpc.WithInsecure(),
	// }
	// err := demo.RegisterGreeterHandlerFromEndpoint(ctx, mux, *grpcServerEndpoint, opts)
	// if err != nil {
	// 	panic(err)
	// }
	http.Handle("/metrics", promhttp.Handler())
	// http.ListenAndServe(":1011", mux)
	http.ListenAndServe(":1011", nil)
}
