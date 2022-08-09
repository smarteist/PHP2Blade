<?php

namespace Tests\ConvertUnit;

use Tests\MainTestCase;

class Comments extends MainTestCase
{
    private $phase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phase = new \App\TranspilePhases\Comments('');
    }

    public function testSingleLineComment()
    {
        $case = "@php //Comment" . PHP_EOL . "echo 'yes!';@endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringContainsString('echo', $this->phase->getOutput());
        $this->assertStringContainsString('yes!', $this->phase->getOutput());
        $this->assertStringContainsString(';', $this->phase->getOutput());
        $this->assertStringContainsString('{{-- Comment --}}', $this->phase->getOutput());

    }

    public function testMultiLineComment()
    {
        $case = "@php /*Comment*/" . PHP_EOL . "echo 'yes!';@endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringContainsString('echo', $this->phase->getOutput());
        $this->assertStringContainsString('yes!', $this->phase->getOutput());
        $this->assertStringContainsString(';', $this->phase->getOutput());
        $this->assertStringContainsString('{{-- Comment --}}', $this->phase->getOutput());

    }

}