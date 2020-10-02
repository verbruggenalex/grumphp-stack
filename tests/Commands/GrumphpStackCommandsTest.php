<?php

declare(strict_types = 1);

namespace VerbruggenAlex\GrumphpStack\Tests\Commands;

use PHPUnit\Framework\TestCase;

final class GrumphpStackCommandsTest extends TestCase
{
    public function testSillyEqualsSilly()
    {
        $this->assertEquals(
            'Silly',
            'Silly'
        );
    }
}
