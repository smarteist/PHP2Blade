<?php

namespace App\TranspilePhases;

use App\Abstracts\TranspilePhase;

class Cleanance extends TranspilePhase
{

    public function doTrans()
    {
        foreach ($this->getPhpTags() as $php) {
            $tag = $php[0];
            $tag = preg_replace('/@php[\s;]*@endphp/m', '', $tag);
            $tag = preg_replace('/@php\s+/m', "@php ", $tag);
            $tag = preg_replace('/\s+@endphp/m', " @endphp", $tag);
            $this->subject = str_replace($php[0], $tag, $this->subject);
        }
    }


}