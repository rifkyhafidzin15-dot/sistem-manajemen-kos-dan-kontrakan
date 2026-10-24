<?php

$address = env('KOSTZENN_ADDRESS', 'Jl. Merdeka No. 45');

$whatsapp = preg_replace('/\D/', '', (string) env('KOSTZENN_WHATSAPP', '6281234567890'));
if (str_starts_with($whatsapp, '0')) {
    $whatsapp = '62'.substr($whatsapp, 1);
}

return [
    'whatsapp' => $whatsapp,
    'address' => $address,
    'maps_embed_url' => env('KOSTZENN_MAPS_EMBED_URL')
        ?: 'https://www.google.com/maps?q='.urlencode($address).'&output=embed',
    'maps_link' => env('KOSTZENN_MAPS_LINK')
        ?: 'https://www.google.com/maps/search/?api=1&query='.urlencode($address),
    'maps_directions' => 'https://www.google.com/maps/dir/?api=1&destination='.urlencode($address),
];