package main

// go tool compile -N -l -S *.go
func main() {
	a := 1
	b := 2
	_ = a + b
	go func() {
		i := 1
		// j := 2
		_ = i + i
	}()
}
