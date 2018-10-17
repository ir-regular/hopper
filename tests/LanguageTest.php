<?php
declare(strict_types=1);

namespace IrRegular\Tests\Hopper\Language;

use function IrRegular\Hopper\Language\add_function_constant_polyfill_to_ns;
use function IrRegular\Hopper\Language\get_defined_functions_in_ns;
use PHPUnit\Framework\TestCase;

function testSubject()
{
    // do nothing
}

class LanguageTest extends TestCase
{
    public function testListingFunctionsInNamespace()
    {
        $testSubjectFn = 'irregular\tests\hopper\language\testsubject';

        $fns = get_defined_functions_in_ns('IrRegular\Tests\Hopper\Language');
        // strings returned in lowercase, as case is not significant
        $this->assertContains($testSubjectFn, $fns);

        // namespace arg tolerant of upper/lowercase, and optional backslash at the end
        $fns = get_defined_functions_in_ns('irregular\tests\\', true);
        $this->assertContains($testSubjectFn, $fns);
    }

    /**
     * @doesNotPerformAssertions
     */
    public function testAutoPolyfillingFunctionsInNamespace()
    {
        add_function_constant_polyfill_to_ns('IrRegular\Tests\Hopper\Language');

        new \ReflectionClass('IrRegular\Tests\Hopper\Language\testsubject');
    }
}
