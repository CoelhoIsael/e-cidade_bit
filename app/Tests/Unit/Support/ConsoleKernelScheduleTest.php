<?php

namespace App\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;

class ConsoleKernelScheduleTest extends TestCase
{
    public function testKernelSchedulesRedesimProcessCommand(): void
    {
        $kernelFile = __DIR__.'/../../../Console/Kernel.php';
        $kernel = file_get_contents($kernelFile);
        $this->assertStringContainsString('redesim:process', $kernel);
    }
}
