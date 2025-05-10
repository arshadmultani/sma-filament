<?php

use Tests\TestCase;
use App\Models\Product;
use App\Models\Division;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, RefreshDatabase::class);

it('can create a product', function () {
    $division = Division::factory()->create();

    $product = Product::create([
        'name' => 'Test Product',
        'price' => 99.99,
        'division_id' => $division->id,
    ]);

    expect($product)->toBeInstanceOf(Product::class);
    expect($product->name)->toBe('Test Product');
    expect($product->division->name)->toBe($division->name);
});

it('can delete a product', function () {
    $division = Division::factory()->create();

    $product = Product::factory()->create([
        'division_id' => $division->id,
    ]);

    $product->delete();

    expect(Product::find($product->id))->toBeNull();
});
