<?php

use BookManager\CPT\BookPostType;
use PHPUnit\Framework\TestCase;
use Brain\Monkey;

/**
 *
 */
class BookPostTypeTest extends TestCase
{
    /**
     * @return void
     */
    protected function setUp(): void
    {
        parent::setUp();
        Monkey\setUp();
        Monkey\Functions\when('__')->returnArg();
    }

    /**
     * @return void
     */
    protected function tearDown(): void
    {
        Monkey\tearDown();
        parent::tearDown();
    }

    /**
     * @return void
     * @throws Monkey\Expectation\Exception\ExpectationArgsRequired
     */
    public function testRegisterPostType()
    {
        Monkey\Functions\expect('register_post_type')
            ->once()
            ->with(
                'book',
                $this->callback(function ($args) {
                    $this->assertArrayHasKey('labels', $args);
                    $expectedLabels = [
                        'name' => 'Books',
                        'singular_name' => 'Book',
                        'add_new_item' => 'Add New Book',
                    ];
                    $this->assertEquals(
                        $expectedLabels,
                        array_intersect_key($args['labels'], $expectedLabels)
                    );

                    $this->assertTrue($args['public']);
                    $this->assertEquals(['slug' => 'books'], $args['rewrite']);

                    $this->assertArrayHasKey('supports', $args);
                    $this->assertContains('title', $args['supports']);
                    $this->assertContains('editor', $args['supports']);
                    $this->assertContains('thumbnail', $args['supports']);
                    $this->assertContains('custom-fields', $args['supports']);

                    return true;
                })
            );

        Monkey\Functions\expect('register_taxonomy')->zeroOrMoreTimes();

        $bookPostType = new BookPostType();
        $bookPostType->register();
    }

    /**
     * @return void
     * @throws Monkey\Expectation\Exception\ExpectationArgsRequired
     */
    public function testRegisterTaxonomies()
    {
        Monkey\Functions\expect('register_post_type')->once();

        Monkey\Functions\expect('register_taxonomy')
            ->times(2)
            ->with(
                $this->logicalOr('publisher', 'author'),
                'book',
                $this->callback(function ($args) {
                    if ($args['labels']['name'] === 'Publishers') {
                        $expectedLabels = [
                            'name' => 'Publishers',
                            'singular_name' => 'Publisher',
                        ];
                        $this->assertEquals(
                            $expectedLabels,
                            array_intersect_key($args['labels'], $expectedLabels)
                        );
                        $this->assertEquals(['slug' => 'publishers'], $args['rewrite']);
                    } elseif ($args['labels']['name'] === 'Authors') {
                        $expectedLabels = [
                            'name' => 'Authors',
                            'singular_name' => 'Author',
                        ];
                        $this->assertEquals(
                            $expectedLabels,
                            array_intersect_key($args['labels'], $expectedLabels)
                        );
                        $this->assertEquals(['slug' => 'authors'], $args['rewrite']);
                    } else {
                        $this->fail('Unexpected taxonomy labels');
                    }

                    $this->assertTrue($args['public']);
                    $this->assertTrue($args['hierarchical']);

                    return true;
                })
            );

        $bookPostType = new BookPostType();
        $bookPostType->register();
    }
}