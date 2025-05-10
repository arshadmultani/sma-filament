<?php

use App\Models\Product;
use Tests\TestCase;
use Tests\Support\AuthenticatesFilamentUsers;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(TestCase::class, AuthenticatesFilamentUsers::class, RefreshDatabase::class);

it('can show the product list page', function () {
    $this->loginAsAdmin();

    $response = $this->get('/admin/products');

    $response->assertOk();
    $response->assertSee('Product');
});

it('displays created product in table', function () {
    $this->loginAsAdmin();

    $product = Product::factory()->create(['name' => 'TestProduct123']);

    $response = $this->get('/admin/products');

    $response->assertSee('TestProduct123');
});
