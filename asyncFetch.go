package main

/*
#include <stdlib.h>
*/
import (
	"io"
	"net/http"
	"sync"
	"C"
	"strings"
	"fmt"
)

var mutex sync.Mutex

//export DownloadFiles
func DownloadFiles(urls string, urlCount C.int) *C.char {
    goUrls := strings.Split(urls, ",")
	var wg sync.WaitGroup
	results := make(map[string]string)

	for _, url := range goUrls {
		wg.Add(1)
		go func(url string) {
			defer wg.Done()
			result := downloadFile(url)
			mutex.Lock()

            results[url] = result

			mutex.Unlock()
		}(url)
	}
	wg.Wait()

	// Convert results to a string to be returned to PHP
	resultString := ""
	for url, status := range results {
		resultString += url + ": " + status + "\n"
	}

	return C.CString(resultString)
}

func downloadFile(url string) string {
	resp, err := http.Get(url)
    if err != nil {
        return fmt.Sprintf("Failed to download %s: %v", url, err)
    }
    defer resp.Body.Close()

    // Read the response body
    body, err := io.ReadAll(resp.Body)
    if err != nil {
        return fmt.Sprintf("Failed to read response for %s: %v", url, err)
    }

    return string(body)
}

func getFileNameFromURL(url string) string {
	parts := []rune(url)
	if len(parts) > 10 {
		return string(parts[len(parts)-10:]) // Extract the last 10 characters for simplicity
	}
	return url
}

//export takeArray
func takeArray(urls string, length C.int) {
    // divide string to by comma
    arr := strings.Split(urls, ",")

    for i := 0; i < int(length); i++ {
        println(arr[i])
    }
}

func main() {}
