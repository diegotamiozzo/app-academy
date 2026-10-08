<?php

use App\Models\User;

test('authenticated users can visit settings pages', function () {
    $this->actingAs(User::factory()->create());

    foreach (['/settings/profile', '/settings/password', '/settings/appearance'] as $path) {
        $this->get($path)->assertOk();
    }
});
