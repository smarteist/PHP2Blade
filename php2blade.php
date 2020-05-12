<?php

use App\BashPrinter;
use App\Converter;
use App\Directory\Extractor;

require_once './vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', true);

if (!isset($argv[1])) {
    // help usage
    echo BashPrinter::getColoredString(PHP_EOL . "Please specify the directory of php files" . PHP_EOL, 'green');
    echo PHP_EOL . "\t" . BashPrinter::getColoredString("php2blade path/to/files", 'white', 'green') . PHP_EOL;
    echo BashPrinter::getColoredString(PHP_EOL . "Or if you want to save your files to a specific directory" . PHP_EOL, 'green');
    echo PHP_EOL . "\t" . BashPrinter::getColoredString("php2blade path/to/files path/to/converted-files", 'white', 'green') . PHP_EOL . PHP_EOL;
} else {
    if (isset($argv[1])) {
        $inputDir = $argv[1];
        $outputDir = isset($argv[2]) ? $argv[2] : __DIR__ . '/out/';
        if (is_dir($inputDir) && file_exists($inputDir)) {
            $files = Extractor::scan($inputDir);
            $converter = new Converter();
            foreach ($files as $file) {

                $outputFile = Extractor::makeOutputFileDirectory($file, $inputDir, $outputDir);
                echo "\t" . BashPrinter::getColoredString("Converting : {$file} ", 'black', 'yellow') . PHP_EOL;
                echo "\t" . BashPrinter::getColoredString("Saved in : {$outputFile}", 'white', 'green') . PHP_EOL . PHP_EOL;

                $converter->convert($file);
                $content = $converter->getConvertedOutput();

                Extractor::createBladeFile($outputFile, $content);
            }
            echo BashPrinter::getColoredString(PHP_EOL . "All Files Converted successfully!" . PHP_EOL . PHP_EOL . PHP_EOL, "green", null);
        } else {
            echo BashPrinter::getColoredString(PHP_EOL . "There is no directory or you dont have permission to access the directory you want . " . PHP_EOL . PHP_EOL . PHP_EOL, "red", null);
        }
    } else {
        echo 'please specify director to covert.' . PHP_EOL;
    }

}