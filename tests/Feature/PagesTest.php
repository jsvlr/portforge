<?php

// Homepage accesiblity
test('example', function () {
    $response = $this->get('/');

    $response->assertStatus(200);
});
