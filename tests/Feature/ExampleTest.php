<?php

test('the management login page is available', function () {
    $response = $this->get('/login');

    $response->assertOk()->assertSee('Masuk pengelola');
});
