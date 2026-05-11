<?php

use App\Models\Category;
use App\Models\Product;

it('filters products by a search term in the title', function () {
    Product::factory()->create(['title' => 'Wireless Headphones', 'slug' => 'wh']);
    Product::factory()->create(['title' => 'Denim Jacket', 'slug' => 'dj']);

    $this->get(route('products.index', ['search' => 'wireless']))
        ->assertOk()
        ->assertSee('Wireless Headphones')
        ->assertDontSee('Denim Jacket');
});

it('filters products by a search term in the description', function () {
    Product::factory()->create([
        'title' => 'Phone Case',
        'slug' => 'pc',
        'description' => 'A protective silicone sleeve.',
    ]);
    Product::factory()->create([
        'title' => 'Mug',
        'slug' => 'mug',
        'description' => 'Ceramic kitchen item.',
    ]);

    $this->get(route('products.index', ['search' => 'silicone']))
        ->assertOk()
        ->assertSee('Phone Case')
        ->assertDontSee('Mug');
});

it('filters products by category slug', function () {
    $electronics = Category::factory()->create(['slug' => 'electronics']);
    $books = Category::factory()->create(['slug' => 'books']);

    Product::factory()->for($electronics)->create(['title' => 'TV', 'slug' => 'tv']);
    Product::factory()->for($books)->create(['title' => 'Novel', 'slug' => 'novel']);

    $this->get(route('products.index', ['category' => 'electronics']))
        ->assertOk()
        ->assertSee('TV')
        ->assertDontSee('Novel');
});

it('combines search and category filters', function () {
    $electronics = Category::factory()->create(['slug' => 'electronics']);
    $books = Category::factory()->create(['slug' => 'books']);

    Product::factory()->for($electronics)->create(['title' => 'Smart Watch', 'slug' => 'sw']);
    Product::factory()->for($electronics)->create(['title' => 'Headphones', 'slug' => 'hp']);
    Product::factory()->for($books)->create(['title' => 'Smart Thinking', 'slug' => 'st']);

    $this->get(route('products.index', ['search' => 'smart', 'category' => 'electronics']))
        ->assertOk()
        ->assertSee('Smart Watch')
        ->assertDontSee('Headphones')
        ->assertDontSee('Smart Thinking');
});

it('shows the empty state when no products match', function () {
    Product::factory()->create(['title' => 'Mug']);

    $this->get(route('products.index', ['search' => 'doesnotexist']))
        ->assertOk()
        ->assertSee('No products match your filters.');
});

it('paginates the storefront listing at 12 per page', function () {
    Product::factory()->count(15)->create();

    $response = $this->get(route('products.index'));
    $response->assertOk()->assertSee('Showing 1');

    $page2 = $this->get(route('products.index', ['page' => 2]));
    $page2->assertOk()->assertSee('Showing 13');
});

it('preserves search and category in pagination links', function () {
    $electronics = Category::factory()->create(['slug' => 'electronics']);
    Product::factory()->count(20)->for($electronics)->sequence(fn ($s) => [
        'title' => "Widget {$s->index}",
        'slug' => "widget-{$s->index}",
    ])->create();

    $response = $this->get(route('products.index', ['search' => 'Widget', 'category' => 'electronics']));
    $response->assertOk();

    expect($response->getContent())->toContain('search=Widget')
        ->and($response->getContent())->toContain('category=electronics');
});

it('lists categories in the filter dropdown', function () {
    Category::factory()->create(['name' => 'Electronics', 'slug' => 'electronics']);
    Category::factory()->create(['name' => 'Books', 'slug' => 'books']);

    $this->get(route('products.index'))
        ->assertOk()
        ->assertSee('All categories')
        ->assertSee('Electronics')
        ->assertSee('Books');
});
