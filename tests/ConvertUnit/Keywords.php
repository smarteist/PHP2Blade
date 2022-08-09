<?php

namespace Tests\ConvertUnit;

use Tests\MainTestCase;

class Keywords extends MainTestCase
{
    private $phase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phase = new \App\TranspilePhases\Keywords('');
    }

    public function testParametrics()
    {
        $case = "@php if(true|false): @endphp <div></div> @php endif @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringContainsString('@if', $this->phase->getOutput());
        $this->assertStringContainsString('@endif', $this->phase->getOutput());
        $this->assertStringContainsString('true|false', $this->phase->getOutput());

        $case = "@php while(true): @endphp <div></div> @php endwhile; @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringContainsString('@while', $this->phase->getOutput());
        $this->assertStringContainsString('@endwhile', $this->phase->getOutput());
        $this->assertStringContainsString('true', $this->phase->getOutput());

        $case = "@php case \"test\": @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringContainsString('@case', $this->phase->getOutput());
        $this->assertStringContainsString('"test"', $this->phase->getOutput());
    }

    public function testNonParametrics()
    {
        $case = "@php break; @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringContainsString('@break', $this->phase->getOutput());


        $case = "@php break @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringContainsString('@break', $this->phase->getOutput());
    }

}