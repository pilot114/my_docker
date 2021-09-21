<?php

include __DIR__ . '/bootstrap.php';

$images = getImages('images', $argv[1] ?? null);

command(
    $images,
    fn($image) => "test -f images/$image/Dockerfile && docker build -t pilot114/$image images/$image"
);

