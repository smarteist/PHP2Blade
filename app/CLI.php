<?php


namespace App;


use App\CLIUtils\CLIBox;
use App\CLIUtils\CLIStr;
use App\Directory\Extractor;
use App\TranspilePhases\BladeExpr;
use App\TranspilePhases\Cleanance;
use App\TranspilePhases\Comments;
use App\TranspilePhases\Keywords;
use App\TranspilePhases\PhpTags;
use Composer\InstalledVersions;

class CLI
{

    /**
     * content of current working file
     * @var string
     */
    private $outputContent;

    public function boot($commands, $options, $switches)
    {
        $destDir = ABSPATH . DIRECTORY_SEPARATOR . 'out/';

        $this->setupOptions($options);
        $this->setupSwitches($switches);

        if (sizeof($commands) < 2) {
            $this->showError(
                "Please specify directory of your source code!\n" .
                "Try 'php php2blade --help' for more information.",
                'Not enough argument:'
            );
            return;
        } else {
            if (sizeof($commands) === 2 && is_dir($commands[1])) {
                $baseDir = $commands[1];
            } elseif (sizeof($commands) === 3 && is_dir($commands[1]) && is_dir($commands[2])) {
                $baseDir = $commands[1];
                $destDir = $commands[2];
            } else {
                $this->showError("Directory is not accessable!", "Invalid directory!");
                //its not a valid directory
                return;
            }

            $files = Extractor::scan($baseDir);

            foreach ($files as $file) {

                $outputFile = Extractor::makeOutputFileDirectory($file, $baseDir, $destDir);
                echo "\t" . CLIStr::create("Converting : {$file} ")
                        ->setColors('black', 'yellow') . PHP_EOL;
                $this->convert($file);
                Extractor::createBladeFile($outputFile, $this->getConvertedOutput());
                echo "\t" . CLIStr::create("Saved in : {$outputFile}")
                        ->bold()
                        ->setColors('white', 'green') . PHP_EOL . PHP_EOL;
            }
        }

        echo CLIStr::create(PHP_EOL . "All Files Converted successfully!" . PHP_EOL . PHP_EOL . PHP_EOL)->setColors("green");
    }

    /**
     * Applies conversion jobs on given source and saves in {$this->outputContent}
     * @param $source string file directory or php content of file.
     */
    public function convert(string $source)
    {
        if (is_file($source)) {
            $this->outputContent = file_get_contents($source);
        } else {
            $this->outputContent = $source;
        }

        $transpiler = new Transpiler($this->outputContent);
        try {
            // The order of the phases is very important
            $transpiler->apply(PhpTags::class)
                ->apply(Comments::class)
                ->apply(Keywords::class)
                ->apply(BladeExpr::class)
                ->apply(Cleanance::class);

        } catch (Exception\TranspilerError $e) {
            $this->showError('Message: ' . $e->getMessage(), "Exception");
            die();
        }
        $this->outputContent = $transpiler->get();

    }

    /**
     * @return string of current output content
     */
    public function getConvertedOutput(): string
    {
        return $this->outputContent;
    }

    private function showHelp()
    {
        $helpContent = [
            CLIStr::tableRow(['BASIC USAGE', 'DESCRIPTION',], [
                "30",
                "50"
            ])->bold()
                ->setColors('red'),
            CLIStr::create(),
            CLIStr::tableRow([
                'php2blade <SRC> <DEST>',
                'Takes 2 directories and transpiles files from source directory <SRC>'
            ], [
                "30",
                "50"
            ]),
            CLIStr::tableRow([
                '',
                'and saves it in the given destinaton <DEST>.'
            ], [
                "30",
                "50"
            ]),
            CLIStr::create(),
            CLIStr::create(),
            CLIStr::tableRow([
                'OPTIONS',
                'DESCRIPTION'
            ], [
                "30",
                "50"
            ])->bold()->setColors('red'),
            CLIStr::create(),
            CLIStr::tableRow([
                '--removecomments',
                'Ignores and deletes all comments.'
            ], [
                "30",
                "50"
            ]),
            CLIStr::tableRow([
                '--keepcomments',
                'Prevents to convert comments to the blade.'
            ], [
                "30",
                "50"
            ]),
            CLIStr::tableRow([
                '--version',
                'Retrieves current version.'
            ], [
                "30",
                "50"
            ])
        ];

        echo (new CLIBox([
            'tableColor' => 'green',
            'titleColor' => 'white',
            'contentColor' => 'cyan',
            'padding' => 1,
            'margin' => 2,
            'align' => 'left',
        ]))->getBox(
            $helpContent,
            CLIStr::create(' Help! ')->setColors('white'),
            CLIStr::create(' PHP2Blade ' . InstalledVersions::getRootPackage()['pretty_version'] . ' ')->setColors('green')
        );
        exit();


    }

    private function showError($message, $title = "Error!")
    {
        echo (new CLIBox([
            'tableColor' => 'red',
            'titleColor' => 'white',
            'contentColor' => 'cyan',
            'padding' => 1,
            'margin' => 2,
            'align' => 'left',
        ]))->getBox(
            $message,
            CLIStr::create(" $title ")->setColors('white'),
            CLIStr::create(' PHP2Blade ' . InstalledVersions::getRootPackage()['pretty_version'] . ' ')->setColors('green')
        );
    }

    private function setupSwitches($switches)
    {
        if (in_array('--help', $switches)) {
            $this->showHelp();
            exit();
        }

        if (in_array('--version', $switches)) {
            echo "PHP2Blade version: " . InstalledVersions::getRootPackage()['pretty_version'] . PHP_EOL;
            exit();
        }

        if (in_array('--removecomments', $switches)) {
            define('REMOVE_COMMENTS', true);
        }

        if (in_array('--keepcomments', $switches)) {
            define('KEEP_COMMENTS', true);
        }
    }

    private function setupOptions($options)
    {
        if (in_array('-v', $options)) {
            echo "PHP2Blade version: " . InstalledVersions::getRootPackage()['pretty_version'] . PHP_EOL;
            exit();
        }
    }

}
