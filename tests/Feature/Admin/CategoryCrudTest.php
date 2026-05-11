<?php

use App\Models\Category;
use App\Models\Product;

it('lists categories with product counts on the admin index', function () {
    $electronics = Category::factory()->create(['name' => 'Electronics']);
    Product::factory()->count(2)->for($electronics)->create();
    Category::factory()->create(['name' => 'Books']);

    $this->get(route('admin.categories.index'))
        ->assertOk()
        ->assertSee('Electronics')
        ->assertSee('Books')
        ->assertSee('New category')
        ->assertSeeInOrder(['Electronics', '2']);
});

it('renders the create form', function () {
    $this->get(route('admin.categories.create'))
        ->assertOk()
        ->assertSee('New category')
        ->assertSee('Slug');
});

it('creates a category from valid input', function () {
    $this->post(route('admin.categories.store'), [
        'name' => 'Gadgets',
        'slug' => 'gadgets',
    ])->assertRedirect(route('admin.categories.index'))
      ->assertSessionHas('status');

    $this->assertDatabaseHas('categories', ['slug' => 'gadgets', 'name' => 'Gadgets']);
});

it('rejects invalid input on store', function () {
    $this->post(route('admin.categories.store'), [])
        ->assertSessionHasErrors(['name', 'slug']);

    expect(Category::count())->toBe(0);
});

it('rejects a duplicate slug on store', function () {
    Category::factory()->create(['slug' => 'taken']);

    $this->post(route('admin.categories.store'), [
        'name' => 'X',
        'slug' => 'taken',
    ])->assertSessionHasErrors('slug');
});

it('renders the edit form pre-filled', function () {
    $category = Category::factory()->create(['name' => 'Electronics', 'slug' => 'electronics']);

    $this->get(route('admin.categories.edit', $category))
        ->assertOk()
        ->assertSee('value="Electronics"', false)
        ->assertSee('value="electronics"', false);
});

it('updates a category', function () {
    $category = Category::factory()->create(['name' => 'Old', 'slug' => 'old']);

    $this->put(route('admin.categories.update', $category), [
        'name' => 'New',
        'slug' => 'new',
    ])->assertRedirect(route('admin.categories.index'));

    expect($category->fresh())
        ->name->toBe('New')
        ->slug->toBe('new');
});

it('allows keeping the same slug on update', function () {
    $category = Category::factory()->create(['slug' => 'keep-me']);

    $this->put(route('admin.categories.update', $category), [
        'name' => 'Same Slug',
        'slug' => 'keep-me',
    ])->assertSessionHasNoErrors();
});

it('rejects a slug already used by another category on update', function () {
    Category::factory()->create(['slug' => 'taken']);
    $category = Category::factory()->create(['slug' => 'mine']);

    $this->put(route('admin.categories.update', $category), [
        'name' => 'X',
        'slug' => 'taken',
    ])->assertSessionHasErrors('slug');
});

it('deletes a category and nulls product foreign keys', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();

    $this->delete(route('admin.categories.destroy', $category))
        ->assertRedirect(route('admin.categories.index'));

    expect(Category::find($category->id))->toBeNull()
        ->and($product->fresh()->category_id)->toBeNull();
});
