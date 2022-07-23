<?php

namespace App\Abstracts;

abstract class TranspilePhase
{
    protected $subject;

    public function __construct($input)
    {
        $this->subject = $input;
    }

    public function getPhpTags()
    {
        $regex = '/@php(\s*[\w\W]*?\s*)@endphp/m';
        preg_match_all($regex, $this->subject, $matches, PREG_SET_ORDER);
        return $matches ?: [];
    }

    public function __toString()
    {
        return $this->getOutput();
    }

    public function getOutput(): string
    {
        $this->doTrans();
        return $this->subject;
    }

    public abstract function doTrans();
}