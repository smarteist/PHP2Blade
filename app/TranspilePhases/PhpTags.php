<?php

namespace App\TranspilePhases;

use App\Abstracts\TranspilePhase;

class PhpTags extends TranspilePhase
{

    public function doTrans()
    {
        $regex = '/<\?php(\s+[\w\W]*?\s*)(?:\?>|\Z)/m';
        $replacement = "@php" . PHP_EOL . "$1" . PHP_EOL . "@endphp";
        $this->subject = preg_replace($regex, $replacement, $this->subject);
    }

}