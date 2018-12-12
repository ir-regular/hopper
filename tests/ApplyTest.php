<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\apply;
use function IrRegular\Hopper\partial;
use PHPUnit\Framework\TestCase;

class ApplyTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testApply()
    {
        $snakeCase = partial('implode', '_');

        $this->assertEquals('key_value', apply($snakeCase, 'key', 'value'));
    }
}
