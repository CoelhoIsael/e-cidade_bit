<?php

namespace App\Tests\Unit\Support;

use PHPUnit\Framework\TestCase;

class AuthConfigTest extends TestCase
{
    public function testUsersProviderUsesLegacyDriver(): void
    {
        $config = require __DIR__.'/../../../../config/auth.php';
        $this->assertSame('legacy', $config['providers']['users']['driver']);
    }
}
