<?php

namespace Swoft\View\Test\Cases;

use PHPUnit\Framework\TestCase;
use Swoft\View\Renderer;

/**
 * Class RendererTest
 */
class RendererTest extends TestCase
{
    public function testRenderPartial(): void
    {
        $config = [
            'viewsPath' => \dirname(__DIR__),
            'layout'    => 'layout.php',
        ];

        $r = new Renderer($config);

        $this->assertEquals('ABC', $r->renderBody('B'));
    }
}
