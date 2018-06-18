<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Nbj\Foundation\ParameterSet;

class ParameterSetTest extends TestCase
{
    /** @test */
    public function it_can_be_created_statically()
    {
        $set = ParameterSet::create([
            'some-key'       => 'some-value',
            'some-other-key' => 'some-other-value',
        ]);

        $this->assertInstanceOf(ParameterSet::class, $set);
        $this->assertCount(2, $set);
        $this->assertTrue($set->has('some-key'));
        $this->assertTrue($set->has('some-other-key'));
    }

    /** @test */
    public function an_empty_set_can_be_created_statically()
    {
        $set = ParameterSet::createEmpty();

        $this->assertInstanceOf(ParameterSet::class, $set);
        $this->assertCount(0, $set);
    }

    /** @test */
    public function it_behaves_like_an_array()
    {
        $set = new ParameterSet([
            'some-key'       => 'some-value',
            'some-other-key' => 'some-other-value',
        ]);

        $this->assertEquals('some-value', $set['some-key']);
        $this->assertTrue(isset($set['some-other-key']));

        unset($set['some-key']);
        $this->assertFalse(isset($set['some-key']));

        $set['some-new-key'] = 'some-new-value';
        $this->assertEquals('some-new-value', $set['some-new-key']);
    }

    /** @test */
    public function it_is_countable()
    {
        $set = new ParameterSet([
            'some-key'       => 'some-value',
            'some-other-key' => 'some-other-value',
        ]);

        $this->assertEquals(2, $set->count());
        $this->assertCount(2, $set);
    }

    /** @test */
    public function it_is_traversable()
    {
        $set = new ParameterSet([
            'some-key'       => 'some-value',
            'some-other-key' => 'some-other-value',
        ]);

        $count = 0;

        foreach ($set as $parameter) {
            $this->assertContains($parameter, $set);

            $count++;
        }

        $this->assertEquals(2, $count);
    }

    /** @test */
    public function it_can_check_if_it_has_a_specific_key()
    {
        $set = new ParameterSet([
            'some-key'       => 'some-value',
            'some-other-key' => 'some-other-value',
        ]);

        $this->assertTrue($set->has('some-key'));
        $this->assertFalse($set->has('this-is-not-a-key-that-exists'));
    }

    /** @test */
    public function it_can_get_the_value_of_a_specific_key()
    {
        $set = new ParameterSet([
            'some-key'       => 'some-value',
            'some-other-key' => 'some-other-value',
        ]);

        $this->assertEquals('some-value', $set->get('some-key'));
    }

    /** @test */
    public function it_returns_null_if_a_key_does_not_exist_or_have_a_value()
    {
        $set = new ParameterSet([
            'some-key'       => 'some-value',
            'some-other-key' => 'some-other-value',
        ]);

        $this->assertNull($set->get('this-key-neither-exists-or-has-a-value'));
    }

    /** @test */
    public function it_can_get_a_list_of_all_its_keys()
    {
        $set = new ParameterSet([
            'some-key'       => 'some-value',
            'some-other-key' => 'some-other-value',
        ]);

        $this->assertEquals([
            'some-key',
            'some-other-key',
        ], $set->keys());
    }
}
