<?php

namespace App\TranspilePhases;

use App\Abstracts\TranspilePhase;

class PhpTags extends TranspilePhase
{

    public function doTrans()
    {
        $this->convertClosingTags();
        $this->convertNonClosingTags();
    }

    public function convertClosingTags()
    {
        $regex = '/<\?php(\s+[\w\W]*?\s+)\?>/m';
        preg_match_all($regex, $this->subject, $matches, PREG_SET_ORDER);
        $matches = is_array($matches) ? $matches : [];
        foreach ($matches as $tag) {
            $output = "@php\n{$tag[1]}\n@endphp";
            $this->subject = str_replace($tag[0], $output, $this->subject);
        }

    }

    public function convertNonClosingTags()
    {
        $regex = '/<\?php(\s+[\w\W]*?)\Z/m';
        preg_match_all($regex, $this->subject, $matches, PREG_SET_ORDER);

        $matches = is_array($matches) ? $matches : [];
        foreach ($matches as $tag) {
            $output = "@php\n{$tag[1]}\n@endphp";
            $this->subject = str_replace($tag[0], $output, $this->subject);
        }
    }

}