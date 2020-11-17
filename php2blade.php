<?php

use App\BashPrinter;
use App\Converter;
use App\Directory\Extractor;

require_once './vendor/autoload.php';

error_reporting(E_ALL);
ini_set('display_errors', true);

if (!isset($argv[1]) || !is_dir(end($argv))) {
    // help usage
    echo BashPrinter::getColoredString(PHP_EOL . "Please specify the directory of php files", 'green');
    echo PHP_EOL . "\t" . BashPrinter::getColoredString("php2blade path/to/files", 'white', 'green') . PHP_EOL;
    echo BashPrinter::getColoredString(PHP_EOL . "Or if you want to save your files to a specific directory", 'green');
    echo PHP_EOL . "\t" . BashPrinter::getColoredString("php2blade path/to/files path/to/converted-files", 'white', 'green') . PHP_EOL . PHP_EOL;
    echo BashPrinter::getColoredString(PHP_EOL . "Add ", 'green')
        . BashPrinter::getColoredString("--removecomments", 'red')
        . BashPrinter::getColoredString(" flag to remove comments of converted files.", 'green');
    echo PHP_EOL . "\t" . BashPrinter::getColoredString("php2blade --removecomments path/to/files path/to/converted-files", 'white', 'green') . PHP_EOL . PHP_EOL;

} else {

    $options = [
        'removeComments' => false,
        'keepComments' => false,
        'inputDir' => null,
        'outputDir' => null,
    ];


    foreach ($argv as $arg) {
        if ($arg === '--removecomments') {
            $options['removeComments'] = true;
        }
        if ($arg === '--keepcomments') {
            $options['keepComments'] = true;
        }
    }
    $options['inputDir'] = is_dir($argv[(sizeof($argv) - 2)]) ? $argv[(sizeof($argv) - 2)] : $argv[(sizeof($argv) - 1)];
    $options['outputDir'] = is_dir($argv[(sizeof($argv) - 2)]) ? $argv[(sizeof($argv) - 1)] . DIRECTORY_SEPARATOR : __DIR__ . '/out/';

    if (is_dir($options['inputDir']) && file_exists($options['inputDir'])) {
        $files = Extractor::scan($options['inputDir']);
        $converter = new Converter();
        $converter->setRemoveComments($options['removeComments']);
        $converter->setKeepComments($options['keepComments']);

        foreach ($files as $file) {

            $outputFile = Extractor::makeOutputFileDirectory($file, $options['inputDir'], $options['outputDir']);
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


}