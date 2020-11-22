<?php

$cidrFile = __DIR__ . '/../../inet/BY/cidr';
$resultFile = 'masscan_result';

$cidrs = explode("\n", file_get_contents($cidrFile));

$ipAll = [];
foreach ($cidrs as $i => $cidr) {
    $command = sprintf("masscan -c masscan.conf %s", $cidr);
    shell_exec($command);

    $data = json_decode(file_get_contents($resultFile));
    if ($data) {
        $ips = array_column($data, 'ip');
        $ipAll = array_merge($ipAll, $ips);
    }
}
$ipsText = implode("\n", $ipAll);
file_put_contents('masscan_result', $ipsText);
echo "done!\n";