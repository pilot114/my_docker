<?php

include __DIR__ . '/bootstrap.php';

$images = getImages('images', $argv[1] ?? null);

command(
    $images,
    fn($image) => "docker images -q pilot114/$image && docker push pilot114/$image"
);
