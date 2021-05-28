# grafana-promethues

```
docker pull bitnami/prometheus:2.26.0
docker run -d -p 9090:9090 bitnami/prometheus:2.26.0

docker pull bitnami/grafana:7.5.4
docker run -d -p 3000:3000 bitnami/grafana:7.5.4
```

```
docker network create example_default

docker-compose up --build
```

```
prometheus http://localhost:9090
grafana http://localhost:3000

admin
admin
```

```
go get github.com/prometheus/client_golang/prometheus
go get github.com/prometheus/client_golang/prometheus/promauto
go get github.com/prometheus/client_golang/prometheus/promhttp
```

```go
package main

import (
	"net/http"

	"github.com/prometheus/client_golang/prometheus/promhttp"
)

func main() {
	http.Handle("/metrics", promhttp.Handler())
	http.ListenAndServe(":2112", nil)
}
```