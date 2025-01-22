<?php
$ffi = FFI::cdef("
        typedef struct { 
            char* p; long n; 
            } GoString;
        char* DownloadFiles(GoString urls, int urlCount);
    ",
    "./asyncFetch.so");

function stringToGoString(FFI $ffi, string $name): FFI\CData
{
    $strChar = str_split($name);
    $c = $ffi->new('char[' . count($strChar) . ']', false);
    foreach ($strChar as $i => $char) {
        $c[$i] = $char;
    }
    $goStr = $ffi->new("GoString");
    $goStr->p = $ffi->cast(FFI::type('char *'), $c);
    $goStr->n = count($strChar);

    return $goStr;
}

$urls = [
    "https://detail-assets.nyc3.digitaloceanspaces.com/assets/v2-assets/backgrounds/above_grade/ag_inlet_2.png",
    "https://detail-assets.nyc3.digitaloceanspaces.com/assets/v2-assets/foregrounds/above_grade_units/inlet_3c_ag.png",
    "https://detail-assets.nyc3.digitaloceanspaces.com/assets/v2-assets/backgrounds/above_grade/ag_2_2.png",
    "https://detail-assets.nyc3.digitaloceanspaces.com/assets/v2-assets/foregrounds/above_grade_units/gb2_3c_ag.png",
    "https://detail-assets.nyc3.digitaloceanspaces.com/assets/v2-assets/backgrounds/above_grade/ag_outlet_2.png",
];
$start = microtime(true);
$c_urls = stringToGoString($ffi, implode(",", $urls));
$result = $ffi->DownloadFiles($c_urls, count($urls));

// Print the result
FFI::string($result);
echo (microtime(true) - $start) . PHP_EOL;
echo "Done with go\n";

$start2 = microtime(true);
foreach ($urls as $url) {
    file_get_contents($url);
}
echo (microtime(true) - $start2) . PHP_EOL;
echo "Done with php\n";
