# Async Fetch

This is a simple exercise that uses PHP FFI to asynchronously download files using go coroutines.

## How to run?

Make sure you have `ffi.enable=true` in your `php.ini`.

### Build asyncfetch.so

    go build -o asyncfetch.so -buildmode=c-shared asyncfetch.go

### Run

    php asyncFetch.php

## References

https://github.com/eislambey/php-ffi-go-example.git