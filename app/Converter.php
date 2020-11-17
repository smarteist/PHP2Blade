<?php


namespace App;


class Converter
{

    /**
     * list of php keywords with sub keywords
     *
     * @var string[]
     */
    const keywords = [
        "if",
        "else",
        "elseif",
        "while" => [
            'continue',
            'break'
        ],
        "foreach" => [
            'continue',
            'break'
        ],
        "switch" => [
            "case",
            'break',
            'default'
        ],
    ];

    /**
     * content of current working file
     * @var string
     */
    private $outputContent;

    /**
     * @var bool indicates comments should be kept or not.
     */
    private $removeComments = false;

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

        // apply conversion on php tags
        foreach ($this->extractPhpTags() as $tag) {
            $output = $this->applyConversion($tag[1]);
            $this->outputContent = str_replace($tag[0], $output, $this->outputContent);
        }

        // if file have a non closing php tag apply conversion jobs again on that block
        foreach ($this->resolveNonClosingTags() as $tag) {
            $output = $this->applyConversion($tag[1] . PHP_EOL);
            $this->outputContent = str_replace($tag[0], $output, $this->outputContent);
        }

    }

    /**
     * @return string of current output content
     */
    public function getConvertedOutput()
    {
        return $this->outputContent;
    }

    /**
     * starts conversion of keywords inside the php tag given
     * @param $tag string is a block of php tag
     * @return string converted php tag
     */
    private function applyConversion(string $tag)
    {
        /*
         * doing conversion jobs one by one
         * Warning : order of conversion is important
         */
        $output = "@php\n{$tag}\n@endphp";

        // convert keywords
        foreach (self::keywords as $key => $val) {

            if (is_array($val)) {
                $keyword = $key;
                $subKeywords = $val;
            } else {
                $keyword = $val;
                $subKeywords = [];
            }
            // convert main keywords
            $output = $this->phpKeywordsToBlade($output, $keyword);
            foreach ($subKeywords as $sub) {
                // convert sub keywords
                $output = $this->phpKeywordsToBlade($output, $sub, true);
            }
        }

        $output = $this->convertCommentsToBlade($output);
        $output = $this->phpEchoToBladeExpression($output);
        $output = $this->cleanEmptyBladeBlocks($output);
        return $output;
    }

    /**
     * Extracts php tags with {<؟php ؟>} format
     * @return array|mixed
     */
    private function extractPhpTags()
    {
        $regex = '/<\?php(\s*[\w\W]*?\s*)\?>/m';
        preg_match_all($regex, $this->outputContent, $matches, PREG_SET_ORDER, 0);
        return $matches ? $matches : [];
    }

    /**
     * Extracts non closing php tags with {<؟php ...} format
     * @return array|mixed
     */
    private function resolveNonClosingTags()
    {
        $regex = '/<\?php(\s*[\w\W]*?)\Z/m';
        preg_match_all($regex, $this->outputContent, $matches, PREG_SET_ORDER, 0);
        return $matches ? $matches : [];
    }

    /**
     * @param $tag string php tag to convert
     * @param $keyword string keyword for conversion
     * @param false $isSub indicates keyword is a sub keyword or not.
     * @return string converted code
     */
    private function phpKeywordsToBlade(string $tag, string $keyword, $isSub = false)
    {
        if ($isSub) {
            preg_match_all("/\s({$keyword}\s*);/m", $tag, $subKeywordMatches, PREG_SET_ORDER, 0);
            preg_match_all("/\s({$keyword}\s*\((.*)\)\s*):/m", $tag, $openingMatches, PREG_SET_ORDER, 0);
            preg_match_all("/\s({$keyword}\s*):/m", $tag, $middleMatches, PREG_SET_ORDER, 0);
            $subKeywordMatches = is_array($subKeywordMatches) ? $subKeywordMatches : [];
            $openingMatches = is_array($openingMatches) ? $openingMatches : [];
            $middleMatches = is_array($middleMatches) ? $middleMatches : [];

            foreach ($subKeywordMatches as $subKeyword) {
                $replacement = PHP_EOL . "@endphp" . PHP_EOL . "@{$keyword}" . PHP_EOL . "@php" . PHP_EOL;
                $tag = str_replace($subKeyword[0], $replacement, $tag);
            }

        } else {
            preg_match_all("/\s({$keyword}\s*\((.*)\)\s*):/m", $tag, $openingMatches, PREG_SET_ORDER, 0);
            preg_match_all("/\s({$keyword}\s*):/m", $tag, $middleMatches, PREG_SET_ORDER, 0);
            preg_match_all("/\s(end{$keyword}\s*);/m", $tag, $closingMatches, PREG_SET_ORDER, 0);
            $openingMatches = is_array($openingMatches) ? $openingMatches : [];
            $middleMatches = is_array($middleMatches) ? $middleMatches : [];
            $closingMatches = is_array($closingMatches) ? $closingMatches : [];

            foreach ($closingMatches as $closeKeyword) {
                $replacement = PHP_EOL . "@endphp" . PHP_EOL . "@end{$keyword}" . PHP_EOL . "@php" . PHP_EOL;
                $tag = str_replace($closeKeyword[0], $replacement, $tag);
            }
        }

        foreach ($openingMatches as $openKeyword) {
            $replacement = PHP_EOL . "@endphp" . PHP_EOL . "@{$keyword} ($openKeyword[2])" . PHP_EOL . "@php" . PHP_EOL;
            $tag = str_replace($openKeyword[0], $replacement, $tag);
        }

        foreach ($middleMatches as $middleKeyword) {
            $replacement = PHP_EOL . "@endphp" . PHP_EOL . "@{$keyword}" . PHP_EOL . "@php" . PHP_EOL;
            $tag = str_replace($middleKeyword[0], $replacement, $tag);
        }

        return $tag;
    }

    /**
     * Converts php comments to blade commenting style
     * @param $tag string php tag to convert
     * @return string output
     */
    private function convertCommentsToBlade($tag)
    {
        $singleLineCommentsRegex = '/\/\/(.*)[\n\r]{1}/m';
        $multiLineCommentsRegex = '/\/\*([\s\S]*?)\*\//m';
        preg_match_all($singleLineCommentsRegex, $tag, $matchesSingle, PREG_SET_ORDER, 0);
        preg_match_all($multiLineCommentsRegex, $tag, $matchesMultiline, PREG_SET_ORDER, 0);
        $matchesSingle = $matchesSingle ? $matchesSingle : [];
        $matchesMultiline = $matchesMultiline ? $matchesMultiline : [];
        foreach (array_merge($matchesSingle, $matchesMultiline) as $comment) {
            if ($this->removeComments) {
                $tag = preg_replace([$singleLineCommentsRegex, $multiLineCommentsRegex], '', $tag);
            } else {
                $tag = "{{-- $comment[1] --}}\n" . preg_replace([$singleLineCommentsRegex, $multiLineCommentsRegex], '', $tag);
            }
        }
        return $tag;
    }

    /**
     * Clean and beautify php blocks, removes blocks that generated in conversion process
     * @param $tag string php tag
     * @return string output
     */
    private function cleanEmptyBladeBlocks($tag)
    {
        $regex = "(@php(\s*)@endphp)";
        preg_match_all($regex, $tag, $emptyBlock, PREG_SET_ORDER, 0);
        $emptyBlock = is_array($emptyBlock) ? $emptyBlock : [];
        foreach ($emptyBlock as $empty) {
            $tag = str_replace($empty[0], '', $tag);
        }
        return str_replace(["@php\n", "\n@endphp"], ["@php", "@endphp"], $tag);
    }

    /**
     * Converts single line echo to blade simple expression
     * @param $tag string to convert
     * @return string output
     */
    private function phpEchoToBladeExpression($tag)
    {
        $regex = "/(@php\s*echo\s*([\w\W].*?)\s*;?\s*@endphp)/m";
        preg_match_all($regex, $tag, $echoBlock, PREG_SET_ORDER, 0);
        $echoBlock = is_array($echoBlock) ? $echoBlock : [];
        foreach ($echoBlock as $echo) {
            $tag = str_replace($echo[0], "{!! $echo[2] !!}", $tag);
        }
        return $tag;
    }

    /**
     * @param bool $removeComments
     */
    public function setRemoveComments(bool $removeComments): void
    {
        $this->removeComments = $removeComments;
    }

}
