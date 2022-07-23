<?php

namespace App\Abstracts;

interface Transpiler
{
    public function apply(string $phase);

    public function get();
}