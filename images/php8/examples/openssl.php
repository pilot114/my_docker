<?php


// https://github.com/fyrkat/php-openssl

/**
 * Пример работы с ключами в асимметричном шифровании
 */
function asymChiperExample()
{
    // генерация пары (PEM сертификат, может иметь разные расширения: .pem, .crt, .cer, и .key)
    $asymKey = openssl_pkey_new();

    // получение пары ключей
    openssl_pkey_export($asymKey, $privateKey);
    $publicKey = openssl_pkey_get_details($asymKey)["key"];

    // получение общего секрета алгоритма открытого ключа
    // Как использовать и зачем, непонятно.
    // openssl_pkey_derive($publicKey, $privateKey, 1024);

    // сохранить приватный ключ в файл
    //openssl_pkey_export_to_file($asymKey, './test.pem');
    // Извлекает открытый ключ из сертификата ??? из текста, из файла и пр.
    //var_dump(openssl_pkey_get_public($publicKey));

    $plainText = '1234 5678 9012 3456';

    // это расшифрует только владелец закрытого ключа
    openssl_public_encrypt($plainText, $cryptText, $publicKey);
    openssl_private_decrypt($cryptText, $decryptText, $privateKey);

    // это мог зашифровать только владелец закрытого ключа (подпись)
    openssl_private_encrypt($plainText, $cryptText2, $privateKey);
    openssl_public_decrypt($cryptText2, $decryptText2, $publicKey);

    var_dump($cryptText);
    var_dump($decryptText);

    var_dump($cryptText2);
    var_dump($decryptText2);
}

/**
 * Новое в php8
 * Работа с S/MIME (эволюция PKCS#7) - стандарт для шифрования и подписи в электронной почте с помощью открытого ключа
 * https://wiki.php.net/rfc/add-cms-support
 */
function mailChiperExample()
{
    $infile = __DIR__ . "/plain.txt";
    $encrypted = tempnam(sys_get_temp_dir(), "cms_dec_basic");
    $outfile = $encrypted . ".out";
    $singleCert = "file://" . __DIR__ . "/cert.crt";
    $privKey = "file://" . __DIR__ . "/private_rsa_1024.key";
    $headers = ["test@test", "testing openssl_cms_encrypt()"];

    openssl_cms_encrypt($infile, $encrypted, $singleCert, $headers);
    $cert = openssl_x509_read($singleCert);
    openssl_cms_decrypt($encrypted, $outfile, $cert, $privKey);

    openssl_cms_sign($infile, $outfile, $singleCert, $privKey, $headers);

    $infile = file_get_contents(__DIR__ . "/cert.p7b");
    $result = [];
    openssl_cms_read($infile, $result);
}

/**
 * сообщение S/MIME
 */
function pkcs7()
{
    $inFilename = "encrypted.msg";  // в этом файле зашифрованное сообщение
    $outFilename = "decrypted.msg"; // убедитесь, что у вас есть права на запись
    $cert = '';
    $key = file_get_contents("nighthawk.pem");

    openssl_pkcs7_encrypt("msg.txt", $inFilename, $key, []);
    openssl_pkcs7_decrypt($inFilename, $outFilename, $cert, $key);

    // Экспортировать файл PKCS7 в массив сертификатов PEM. незадокументировано.
    openssl_pkcs7_read($inFilename, $certs);

    // Подписать сообщение S/MIME
    $signcert = "file://mycert.pem";
    $privateKey = "file://mycert.pem";
    openssl_pkcs7_sign($inFilename, $outFilename, $signcert, $privateKey, $headers = []);
    // Проверить подпись сообщения S/MIME
    openssl_pkcs7_verify($inFilename, null);
}

/**
 * x509 - стандарт сертификата открытого ключа. Решает проблему подделки открытых ключей,
 * путем подписания их центрами сертификации
 */
function x509()
{
    $certdata = 'file.pem';
    $data = openssl_x509_read($certdata);

    // Вычисляет отпечаток или дайджест, заданный сертификатом X.509
    echo openssl_x509_fingerprint($data);

    // Разобрать сертификат X509 и получить массив с данными о нем
    echo openssl_x509_parse($data);

    // проверка соответствия приватного ключа сертификату
    $privateKey = '';
    echo openssl_x509_check_private_key($data, $privateKey);

    // проверка, можно ли использовать сертификат для определенной цели
    $purpose = 123;
    $ca = []; // File and directory names that specify the locations of trusted CA files
    $untrustedfile = ''; // PEM encoded file holding certificates that can be used to help verify this certificate
    echo openssl_x509_checkpurpose($data, $purpose, $ca, $untrustedfile);

    // проверка цифровой подписи сертификата x509 с помощью публичного ключа
    // https://www.php.net/manual/ru/function.openssl-x509-verify.php
    $publicKey = '';
    echo openssl_x509_verify($data, $publicKey);

    // экспорт в переменную, с текстовым описанием
    openssl_x509_export($data, $output, false);
    // или в файл
    // openssl_x509_export_to_file($data, $output, false);
}

/**
 * CSR - ключ, генерируемый при запросе на подпись SSL сертификата.
 * Можно использовать для генерации самоподписанных сертификатов
 */
function csr()
{
    $dn = [
        "countryName" => "GB",
        "stateOrProvinceName" => "Somerset",
        "localityName" => "Glastonbury",
        "organizationName" => "The Brain Room Limited",
        "organizationalUnitName" => "PHP Documentation Team",
        "commonName" => "Wez Furlong",
        "emailAddress" => "wez@example.com"
    ];

    $privateKey = openssl_pkey_new([
        "private_key_bits" => 2048,
        "private_key_type" => OPENSSL_KEYTYPE_RSA,
    ]);
    // Создание CSR
    $csr = openssl_csr_new($dn, $privateKey, ['digest_alg' => 'sha256']);
    // Создание самоподписанного сертификата со сроком жизни 365 дней
    $x509 = openssl_csr_sign($csr, null, $privateKey, $days = 365, ['digest_alg' => 'sha256']);

    openssl_csr_export($csr, $output);
    $outputFile = '';
    openssl_csr_export_to_file($csr, $outputFile);

    $publicKey = openssl_csr_get_public_key($csr);
    print_r(openssl_csr_get_subject($csr));
}

/**
 * Создание нового подписанного открытого ключа с вызовом (Netscape SPKI)
 * https://ru.wikipedia.org/wiki/SPKI
 */
function spki()
{
    $pkey = openssl_pkey_new();
    openssl_spki_new($pkey, $spkac);

    // Проверяет подписанный открытый ключ и вызов
    echo openssl_spki_verify($spkac);

    $pubKey = openssl_spki_export($spkac);
    $challenge = openssl_spki_export_challenge($spkac);
}

/**
 * Работа с хранилищем сертификатов PKCS#12 (Формат файлов для хранения асимметричных ключей)
 */
function pkcs12()
{
    $certStore = file_get_contents("/certs/file.p12");
    openssl_pkcs12_read($certStore, $certInfo, "my_secret_pass");

    $x509 = '';
    $privateKey = '';
    $pass = '';
    openssl_pkcs12_export($x509, $cert, $privateKey, $pass, []);
//    openssl_pkcs12_export_to_file($x509, $filename, $privateKey, $pass, []);
}

/**
 * Информация по алгоритмам шифрования, хеширования, кривых по ECC и пр.
 */
function getMethodInfo($filterLeaks = true)
{
    $c = [];
    $ciphers = openssl_get_cipher_methods();
    if ($filterLeaks) {
        $ciphers = array_filter($ciphers, fn($n) => stripos($n, "ecb") === false);
        $ciphers = array_filter($ciphers, fn($n) => stripos($n, "des") === false);
        $ciphers = array_filter($ciphers, fn($n) => stripos($n, "rc2") === false);
        $ciphers = array_filter($ciphers, fn($n) => stripos($n, "rc4") === false);
        $ciphers = array_filter($ciphers, fn($n) => stripos($n, "md5") === false);
    }
    return [
        'chipers' => $ciphers,
        'hashes' => openssl_get_md_methods(),
        'locations' => openssl_get_cert_locations(),
        'curves' => openssl_get_curve_names()
    ];
}

/**
 * Симметричное шифрование (пример)
 */
function encodeExample($chiperName, $text, $key)
{
    // основная функция для получения случайных данных
    $key = openssl_random_pseudo_bytes(1024);

    $ivlen = openssl_cipher_iv_length($chiperName);
    $iv = openssl_random_pseudo_bytes($ivlen);
    $ciphertext = openssl_encrypt($text, $chiperName, $key, $options=0, $iv, $tag);

    $text = openssl_decrypt($ciphertext, $chiperName, $key, $options=0, $iv, $tag);
}

/**
 * Хэширование данных
 */
function hash($name, $data)
{
    return openssl_digest($data, $name, false);
}

/**
 * Генерирует строки PKCS5 v2 PBKDF2
 */
function pbkdf2Hash()
{
    $password = 'yOuR-pAs5w0rd-hERe';
    $salt = openssl_random_pseudo_bytes(12);
    $keyLength = 40;
    $iterations = 10000;
    $generated_key = openssl_pbkdf2($password, $salt, $keyLength, $iterations, 'sha256');
    return base64_encode($generated_key);
}

/**
 * Вычисление общего ключа для системы Диффи-Хеллмана
 */
function commonKey($pubKey, $privateKey)
{
    return openssl_dh_compute_key($pubKey, $privateKey);
}

// получить последнюю ошибку из очереди ошибок OpenSSL (можно вызывая подряд получить все)
// echo openssl_error_string();

$info = getMethodInfo();

var_dump($info);
die();

//string(12) "openssl_open"
//string(12) "openssl_seal"

//string(12) "openssl_sign"
//string(14) "openssl_verify"
