<?php

namespace App\TranspilePhases;

use App\Abstracts\TranspilePhase;

class Comments extends TranspilePhase
{

    public function doTrans()
    {
        if (defined('KEEP_COMMENTS'))
            return;
        foreach ($this->getPhpTags() as $php) {
            $tag = $php[0];
            $singleLineCommentsRegex = '/\/\/([^\n\r]*?)[\n\r]/';
            $multiLineCommentsRegex = '/\/\*+([\w\W]*?)\*+\//m';
            preg_match_all($singleLineCommentsRegex, $tag, $matchesSingle, PREG_SET_ORDER, 0);
            preg_match_all($multiLineCommentsRegex, $tag, $matchesMultiline, PREG_SET_ORDER, 0);
            $matchesSingle = $matchesSingle ?: [];
            $matchesMultiline = $matchesMultiline ?: [];
            foreach (array_merge($matchesSingle, $matchesMultiline) as $comment) {
                if (defined('REMOVE_COMMENTS')) {
                    $tag = preg_replace([$singleLineCommentsRegex, $multiLineCommentsRegex], '', $tag);
                } else {
                    $tag = "{{-- $comment[1] --}}" . PHP_EOL . preg_replace([$singleLineCommentsRegex, $multiLineCommentsRegex], '', $tag);
                }
            }
            $this->subject = str_replace($php[0], $tag, $this->subject);
        }
    }

}