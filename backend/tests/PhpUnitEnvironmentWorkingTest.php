<?php

namespace App\Tests;

use PHPUnit\Framework\TestCase;

class PhpUnitEnvironmentWorkingTest extends TestCase
{
    public function testAdditionOfTwoNumbers(): void
    {
        $this->assertEquals(4, 2+2);
    }
}