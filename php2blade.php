<?php

use App\Converter;
use App\Directory\Extractor;

require_once './vendor/autoload.php';

//error_reporting(E_ALL);
//ini_set('display_errors', true);

if (!isset($argv[1])) {
    echo "Usage";
} else {
    if (isset($argv[1])) {
        $inputDir = is_dir($argv[1]) ? $argv[1] : __DIR__ . $argv[2];
        $outputDir = isset($argv[2]) ? $argv[2] : __DIR__ . '/out/';
        if (is_dir($inputDir)) {
            $files = Extractor::scan($inputDir);
            $converter = new Converter();
            foreach ($files as $file) {
                $converter->convert($file);
                $content = $converter->getConvertedOutput();
                $outputFile = Extractor::makeOutputFileDirectory($file, $inputDir, $outputDir);
                Extractor::createBladeFile($outputFile, $content);
            }
        } else {

        }
    } else {
        echo 'please specify director to covert.' . PHP_EOL;
    }

}