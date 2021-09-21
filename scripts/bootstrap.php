<?php

function getImages(string $imageDir, ?string $imageName): array
{
    if ($imageName) {
        if (!is_dir("./$imageDir/$imageName")) {
            echo sprintf("Указан несуществующий образ: %s\n", $imageName);
            exit;
        }
        $images = [$imageName];
    } else {
        $images = glob("./$imageDir/*");
        $images = array_map(fn($x) => str_replace("./$imageDir/", '', $x), $images);
    }
    return $images;
}

function command(array $images, callable $cb)
{
    foreach ($images as $image) {
        $command = $cb($image);
        echo "> $command\n";
        shell_exec($command);
    }
}