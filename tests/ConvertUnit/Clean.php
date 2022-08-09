<?php

namespace Tests\ConvertUnit;

use Tests\MainTestCase;

class Clean extends MainTestCase
{
    private $phase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phase = new \App\TranspilePhases\Cleanance('');
    }

    public function testClean()
    {
        $case = "@php ; ; ;  ; ; @endphp @php  @endphp @php \n @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringNotContainsString('@php', $this->phase->getOutput());
        $this->assertStringNotContainsString('@endphp', $this->phase->getOutput());
        $this->assertStringNotContainsString(';', $this->phase->getOutput());
    }

}