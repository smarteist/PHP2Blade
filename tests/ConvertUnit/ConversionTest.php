<?php


namespace Tests\ConvertUnit;


use App\CLI;
use Tests\MainTestCase;

class ConversionTest extends MainTestCase
{

    private $converter;

    protected function setUp(): void
    {
        $this->converter = new CLI();
        parent::setUp();
    }


    public function testClosingTag()
    {
        $this->converter->convert("<?php <?php for(;;;){} ?>");
        $out = $this->converter->getConvertedOutput();
        $this->assertStringContainsString("@php", $out);
        $this->assertStringContainsString("@endphp", $out);
    }


    public function testNonClosingTag()
    {
        $this->converter->convert("<?php for(;;;){}");
        $out = $this->converter->getConvertedOutput();
        $this->assertStringContainsString("@php", $out);
        $this->assertStringContainsString("@endphp", $out);
    }


    public function testBladeExpression()
    {
        $this->converter->convert("<?php echo 'test'");
        $out = $this->converter->getConvertedOutput();
        $this->assertEquals("{!! 'test' !!}", $out);
    }


    public function testBladeExpressionInHtml()
    {
        $this->converter->convert("<h2 attr=\"<?php echo 'test'?>\"></h2>");
        $out = $this->converter->getConvertedOutput();
        $this->assertEquals("<h2 attr=\"{!! 'test' !!}\"></h2>", $out);
    }

    public function testWhileLoop()
    {
        $this->converter->convert("<?php while(true): ?> <?php echo \"infinity\"; ?> <?php endwhile; ?>");
        $out = $this->converter->getConvertedOutput();
        $this->assertStringContainsString("@endwhile", $out);
        $this->assertStringContainsString("@while", $out);
    }

    public function testForEachLoop()
    {
        $this->converter->convert("<?php foreach(true): ?> <?php echo \"infinity\"; ?> <?php endforeach; ?>");
        $out = $this->converter->getConvertedOutput();
        $this->assertStringContainsString("@endforeach", $out);
        $this->assertStringContainsString("@foreach", $out);
    }


    public function testIfStatements()
    {
        $this->converter->convert("<?php if(true): ?> <?php echo \"infinity\"; ?> <?php elseif(false): ?> <?php echo \"infinity\"; ?> <?php else: ?><?php echo \"infinity\"; ?><?php endif; ?>");
        $out = $this->converter->getConvertedOutput();
        $this->assertStringContainsString("@if", $out);
        $this->assertStringContainsString("@elseif", $out);
        $this->assertStringContainsString("@else", $out);
        $this->assertStringContainsString("@endif", $out);
    }

}