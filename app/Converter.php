<?php


namespace App;


class Converter
{

    private $keywords = [
        "if",
        "else",
        "elseif",
        "while",
        "foreach",
    ];
    private $outputContent;
    private $removeComments = false;

    public function convert($file)
    {
        $this->outputContent = file_get_contents($file);

        // apply conversion on php tags
        foreach ($this->extractPhpTags() as $tag) {
            $output = $this->applyConversion($tag[1]);
            $this->outputContent = str_replace($tag[0], $output, $this->outputContent);
        }

        // if file have a non closing php tag apply conversion jobs again
        foreach ($this->resolveNonClosingTags() as $tag) {
            $output = $this->applyConversion($tag[1] . PHP_EOL);
            $this->outputContent = str_replace($tag[0], $output, $this->outputContent);
        }

    }

    public function getConvertedOutput()
    {
        return $this->outputContent;
    }

    private function applyConversion($tag)
    {
        /*
         * doing conversion jobs one by one
         * Warning : order of conversion is important
         */
        $output = "@php\n{$tag}\n@endphp";
        $output = $this->phpKeywordsToBlade($output);
        $output = $this->convertCommentsToBlade($output);
        $output = $this->cleanEmptyBladeBlocks($output);
        $output = $this->phpEchoToBladeExpression($output);
        return str_replace(["@php\n", "\n@endphp"], ["@php", "@endphp"], $output);
    }

    private function extractPhpTags()
    {
        $regex = '/<\?php(\s*[\w\W]*?\s*)\?>/m';
        preg_match_all($regex, $this->outputContent, $matches, PREG_SET_ORDER, 0);
        return $matches ? $matches : array();
    }

    private function resolveNonClosingTags()
    {
        $regex = '/<\?php(\s*[\w\W]*?)\Z/m';
        preg_match_all($regex, $this->outputContent, $matches, PREG_SET_ORDER, 0);
        return $matches ? $matches : array();
    }

    private function phpKeywordsToBlade($tag)
    {
        foreach ($this->keywords as $keyword) {
            $openingRegex = "/\s({$keyword}\s*\((.*)\)\s*):/m";
            $middleRegex = "/\s($keyword\s*):/m";
            $closingRegex = "/\s(end{$keyword}\s*);/m";
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

    private function convertCommentsToBlade($tag)
    {
        $singleLineCommentsRegex = '/\/\/\s*(.+?)(?=[\n\r]|\*\))/m';
        $multiLineCommentsRegex = '/\/\*\*?(.*)\*\//m';
        preg_match_all($singleLineCommentsRegex, $tag, $matchesSingle, PREG_SET_ORDER, 0);
        preg_match_all($multiLineCommentsRegex, $tag, $matchesMultiline, PREG_SET_ORDER, 0);
        $matchesSingle = $matchesSingle ? $matchesSingle : array();
        $matchesMultiline = $matchesMultiline ? $matchesMultiline : array();
        foreach ($matchesSingle as $comment) {
            if ($this->removeComments) {
                $tag = str_replace($comment[0], '', $tag);
            } else {
                $tag = "{{-- $comment[1] --}}\n" . str_replace($comment[0], '', $tag);
            }
        }
        foreach ($matchesMultiline as $comment) {
            if ($this->removeComments) {
                $tag = str_replace($comment[0], '', $tag);
            } else {
                $tag = "{{-- $comment[1] --}}\n" . str_replace($comment[0], '', $tag);
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
        $regex = "/(@php\s*echo\s*([\w\W].*?)\s*;?\s*@endphp)/m";
        preg_match_all($regex, $tag, $echoBlock, PREG_SET_ORDER, 0);
        $echoBlock = is_array($echoBlock) ? $echoBlock : array();
        foreach ($echoBlock as $echo) {
            $tag = str_replace($echo[0], "{!! $echo[2] !!}", $tag);
        }
        return $tag;
    }

}
