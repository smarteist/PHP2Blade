<?php

namespace Tests\ConvertUnit;

use Tests\MainTestCase;

class PhpTag extends MainTestCase
{
    private $phase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->phase = new \App\TranspilePhases\PhpTags('');
    }

    public function testTags()
    {
        $case = "something else<?php echo 'yes!';?> <br/> <?php die();";
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringContainsString('@php', $this->phase->getOutput());
        $this->assertStringContainsString('@endphp', $this->phase->getOutput());
        $this->assertStringContainsString('echo', $this->phase->getOutput());
        $this->assertStringContainsString('yes!', $this->phase->getOutput());
        $this->assertStringContainsString(';', $this->phase->getOutput());
        $this->assertStringContainsString('die();', $this->phase->getOutput());
        $this->assertStringEndsWith('@endphp', $this->phase->getOutput());
        $this->assertEquals(2, substr_count($this->phase->getOutput(), '@php'));
        $this->assertEquals(2, substr_count($this->phase->getOutput(), '@endphp'));
    }


    public function testNonClosingTags()
    {
        $case = substr("\<?php echo 'yes!'; die();", 1);
        $this->phase->__construct($case);
        $this->phase->doTrans();
        $this->assertStringContainsString('@php', $this->phase->getOutput());
        $this->assertStringContainsString('@endphp', $this->phase->getOutput());
        $this->assertStringContainsString('echo', $this->phase->getOutput());
        $this->assertStringContainsString("'yes!'", $this->phase->getOutput());
        $this->assertStringContainsString('die()', $this->phase->getOutput());
        $this->assertStringEndsWith('@endphp', $this->phase->getOutput());
        $this->assertEquals(2, substr_count($this->phase->getOutput(), ';'));
        $this->assertEquals(1, substr_count($this->phase->getOutput(), '@php'));
        $this->assertEquals(1, substr_count($this->phase->getOutput(), '@endphp'));
    }

}