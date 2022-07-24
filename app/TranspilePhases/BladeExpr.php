<?php

namespace App\TranspilePhases;

use App\Abstracts\TranspilePhase;

class BladeExpr extends TranspilePhase
{

    public function doTrans()
    {
        $regex = "/@php\s+echo\s+([^;]+?)\s*;*\s+@endphp/m";
        $this->subject = preg_replace($regex, "{!! $1 !!}", $this->subject);
    }


}