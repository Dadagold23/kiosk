<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class FrontendPagesTest extends TestCase
{
    use RefreshDatabase;

    public function test_frontend_module_pages_render_successfully(): void
    {
        foreach (['/shop', '/services', '/consultancy', '/booking', '/emergency'] as $path) {
            $this->get($path)->assertOk();
        }
    }
}
