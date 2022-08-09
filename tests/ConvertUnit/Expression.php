<?php

namespace Tests\ConvertUnit;

use Tests\MainTestCase;

class Expression extends MainTestCase
{
    private $phase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phase = new \App\TranspilePhases\BladeExpr('');
    }

    public function testExprWithoutSemicolon()
    {
        $case = "@php echo 'Hello Guys!' @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringNotContainsString('@php', $this->phase->getOutput());
        $this->assertStringNotContainsString('@endphp', $this->phase->getOutput());
        $this->assertStringNotContainsString('echo', $this->phase->getOutput());
        $this->assertStringNotContainsString(';', $this->phase->getOutput());
        $this->assertStringContainsString('{!! ', $this->phase->getOutput());
        $this->assertStringContainsString(' !!}', $this->phase->getOutput());
        $this->assertStringContainsString("'Hello Guys!'", $this->phase->getOutput());
    }


    public function testExprFunction()
    {
        $case = "@php echo somefunc('arg'); @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringNotContainsString('@php', $this->phase->getOutput());
        $this->assertStringNotContainsString('@endphp', $this->phase->getOutput());
        $this->assertStringNotContainsString('echo', $this->phase->getOutput());
        $this->assertStringNotContainsString(';', $this->phase->getOutput());
        $this->assertStringContainsString('{!! ', $this->phase->getOutput());
        $this->assertStringContainsString(' !!}', $this->phase->getOutput());
        $this->assertStringContainsString("'arg'", $this->phase->getOutput());
        $this->assertStringContainsString("somefunc", $this->phase->getOutput());
        $this->assertStringContainsString("(", $this->phase->getOutput());
        $this->assertStringContainsString(")", $this->phase->getOutput());
    }

    public function testExpr()
    {
        $case = "@php echo 'Hello Guys! I have semicolon =>'; @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringNotContainsString('@php', $this->phase->getOutput());
        $this->assertStringNotContainsString('@endphp', $this->phase->getOutput());
        $this->assertStringNotContainsString('echo', $this->phase->getOutput());
        $this->assertStringNotContainsString(';', $this->phase->getOutput());
        $this->assertStringContainsString('{!! ', $this->phase->getOutput());
        $this->assertStringContainsString(' !!}', $this->phase->getOutput());
        $this->assertStringContainsString("'Hello Guys! I have semicolon =>'", $this->phase->getOutput());
    }

    public function testExprWithConcatination()
    {
        $case = "@php echo 'Hello Guys!' . PHP_EOL . ' We're testing'; @endphp";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringNotContainsString('@php', $this->phase->getOutput());
        $this->assertStringNotContainsString('@endphp', $this->phase->getOutput());
        $this->assertStringNotContainsString('echo', $this->phase->getOutput());
        $this->assertStringNotContainsString(';', $this->phase->getOutput());
        $this->assertStringContainsString('{!! ', $this->phase->getOutput());
        $this->assertStringContainsString(' !!}', $this->phase->getOutput());
        $this->assertStringContainsString("' We're testing'", $this->phase->getOutput());
        $this->assertStringContainsString("PHP_EOL", $this->phase->getOutput());
        $this->assertStringContainsString("'Hello Guys!'", $this->phase->getOutput());
    }
}