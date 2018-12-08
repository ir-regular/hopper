<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper;

use function IrRegular\Hopper\apply;
use PHPUnit\Framework\TestCase;

class ApplyTest extends TestCase
{
    use CollectionSetUpTrait;

    public function testApply()
    {
        $formatPeriod = function (string $name, int $length) {
            // PSA: yes, I could have done a singular/plural based on $length
            // No, this is not how you do plurals if you want to have a hope of internationalising your code.
            // For one thing, some languages have more than one plural form, depending on count.
            return "$name lasts $length days";
        };

        $this->assertEquals('weekend lasts 2 days', apply($formatPeriod, ['weekend', 2]));
    }
}
