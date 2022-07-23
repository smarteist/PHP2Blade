<?php

namespace App;

use App\Exception\TranspilerError;
use App\TranspilePhases\PhpTags;

class Transpiler implements Abstracts\Transpiler
{
    private $subject;

    public function __construct($input)
    {
        $this->subject = $input;
    }

    /**
     * @param string $phase
     * @return Transpiler
     * @throws TranspilerError
     */
    public function apply(string $phase): Transpiler
    {
        if (class_exists($phase)) {
            $this->subject = new $phase($this->subject);
        } else {
            throw new TranspilerError("Unknown conversion phase: , class " . $phase . " not found!");
        }
        return $this;
    }

    public function get(): string
    {
        return strval($this->subject);
    }
}