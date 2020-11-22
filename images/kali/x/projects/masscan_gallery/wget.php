<?php

/**
 * Первая идея - вытягивать главные страницы.
 * От идеи отказался - для галереи полезнее делать скриншоты.
 */

$filename = 'out_masscan';
$previewDir = __DIR__ . '/preview';

$lines = explode("\n", file_get_contents($filename));
foreach ($lines as $i => $line) {
    $parts = explode(" ", $line);
    if (isset($parts[3])) {
        $ip = $parts[3];
        $command = sprintf("wget --tries=1 -q -p -k --no-check-certificate -F --content-on-error -P %s %s", $previewDir, $ip);
        shell_exec($command);
    }
}
