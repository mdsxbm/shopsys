<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Component\Transformers;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Form\Transformers\RemoveWhitespacesTransformer;

class RemoveWhitespacesTransformerTest extends TestCase
{
    public static function transformValuesProvider()
    {
        return [
            ['value' => 'foo bar', 'expected' => 'foobar'],
            ['value' => 'FooBar', 'expected' => 'FooBar'],
            ['value' => '  foo  bar  ', 'expected' => 'foobar'],
            ['value' => "foo\t", 'expected' => 'foo'],
            ['value' => "fo\no", 'expected' => 'foo'],
            ['value' => null, 'expected' => null],
        ];
    }

    /**
     * @param mixed $value
     * @param mixed $expected
     */
    #[DataProvider('transformValuesProvider')]
    public function testReverseTransform($value, $expected)
    {
        $transformer = new RemoveWhitespacesTransformer();
        $this->assertSame($expected, $transformer->reverseTransform($value));
    }
}
