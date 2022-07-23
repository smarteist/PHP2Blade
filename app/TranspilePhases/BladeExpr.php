<?php

namespace App\TranspilePhases;

use App\Abstracts\TranspilePhase;

class BladeExpr extends TranspilePhase
{

    public function doTrans()
    {
        foreach ($this->getPhpTags() as $php) {

            $tag = $php[0];
            $regex = "/(@php\s+echo\s+([\w\W].*?)\s*;?\s+@endphp)/m";
            preg_match_all($regex, $tag, $echoBlock, PREG_SET_ORDER, 0);
            $echoBlock = is_array($echoBlock) ? $echoBlock : [];
            foreach ($echoBlock as $echo) {
                $tag = str_replace($echo[0], "{!! $echo[2] !!}", $tag);
            }

            $this->subject = str_replace($php[0], $tag, $this->subject);
        }
    }


}