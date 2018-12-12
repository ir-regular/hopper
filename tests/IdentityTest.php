<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\identity;
use PHPUnit\Framework\TestCase;

class IdentityTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testIdentity()
    {
        $x = 1;
        $this->assertEquals($x, identity($x));

        $x = [1, 2, 3];
        $this->assertEquals($x, identity($x));

        $x = new \ArrayObject($x);
        $this->assertEquals($x, identity($x));
    }
}
