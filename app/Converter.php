<?php


namespace App;


class Converter
{

    public $keywords = [
        "if",
        "else",
        "elseif",
        "while",
        "foreach",
    ];

    private $fileContent;

    private $outputContent;

    public function convert($file)
    {
        $this->fileContent = file_get_contents($file);
        $this->outputContent = $this->fileContent;

        foreach ($this->extractPhpTags() as $tag) {
            $output = $this->applyConversion($tag[0]);
            $this->outputContent = str_replace($tag[0], $output, $this->outputContent);
        }

        // if file have a non closing php tag apply conversion jobs again
        if ($this->resolveNonClosingTags()) {
            $this->outputContent = $this->applyConversion($this->outputContent);
        }

    }

    public function getConvertedOutput()
    {
        return $this->outputContent;
    }

    private function applyConversion($part)
    {
        $output = $this->phpTagToBlade($part);
        $output = $this->phpKeywordsToBlade($output);
        $output = $this->phpEchoToBladeExpression($output);
        $output = $this->cleanEmptyBladeBlocks($output);
        return $output;
    }

    private function extractPhpTags()
    {
        $regex = '/<\?php([\w\W]*?)\?>/mi';
        preg_match_all($regex, $this->fileContent, $matches, PREG_SET_ORDER, 0);
        return $matches ? $matches : array();
    }

    private function resolveNonClosingTags()
    {
        // last php tags without closing checking
        if (strpos($this->outputContent, '<?php') !== false) {
            $this->outputContent = str_replace('<?php', '@php' . PHP_EOL, $this->outputContent);
            $this->outputContent .= '@endphp';
            return true;
        }

        return false;
    }

    private function phpTagToBlade($tag)
    {
        $tag = str_replace('<?php', '@php', $tag);
        $tag = str_replace('?>', '@endphp', $tag);
        return $tag;
    }

    private function phpKeywordsToBlade($tag)
    {
        foreach ($this->keywords as $keyword) {
            $openingRegex = "/({$keyword}\s*\((.*)\)\s*):/m";
            $middleRegex = "/($keyword\s*):/m";
            $closingRegex = "/(end{$keyword}\s*);/m";
            preg_match_all($openingRegex, $tag, $openingMatches, PREG_SET_ORDER, 0);
            preg_match_all($middleRegex, $tag, $middleMatches, PREG_SET_ORDER, 0);
            preg_match_all($closingRegex, $tag, $closingMatches, PREG_SET_ORDER, 0);
            $openingMatches = is_array($openingMatches) ? $openingMatches : array();
            $middleMatches = is_array($middleMatches) ? $middleMatches : array();
            $closingMatches = is_array($closingMatches) ? $closingMatches : array();

            foreach ($openingMatches as $openKeyword) {
                $replacement = PHP_EOL . "@endphp" . PHP_EOL . "@{$keyword} ($openKeyword[2])" . PHP_EOL . "@php" . PHP_EOL;
                $tag = str_replace($openKeyword[0], $replacement, $tag);
            }

            foreach ($middleMatches as $middleKeyword) {
                $replacement = PHP_EOL . "@endphp" . PHP_EOL . "@{$keyword}" . PHP_EOL . "@php" . PHP_EOL;
                $tag = str_replace($middleKeyword[0], $replacement, $tag);
            }

            foreach ($closingMatches as $closeKeyword) {
                $replacement = PHP_EOL . "@endphp" . PHP_EOL . "@end{$keyword}" . PHP_EOL . "@php" . PHP_EOL;
                $tag = str_replace($closeKeyword[0], $replacement, $tag);
            }

        }

        return $tag;
    }

    private function cleanEmptyBladeBlocks($tag)
    {
        $regex = "(@php(\s*)@endphp)";
        preg_match_all($regex, $tag, $emptyBlock, PREG_SET_ORDER, 0);
        $emptyBlock = is_array($emptyBlock) ? $emptyBlock : array();
        foreach ($emptyBlock as $empty) {
            $tag = str_replace($empty[0], '', $tag);
        }
        return $tag;
    }

    private function phpEchoToBladeExpression($tag)
    {
        $regex = "(@php\s*echo\s*(.*)\s*;\s*@endphp)";
        preg_match_all($regex, $tag, $echoBlock, PREG_SET_ORDER, 0);
        $echoBlock = is_array($echoBlock) ? $echoBlock : array();
        foreach ($echoBlock as $echo) {
            $tag = str_replace($echo[0], "{{ $echo[1] }}", $tag);
        }
        return $tag;
    }
}