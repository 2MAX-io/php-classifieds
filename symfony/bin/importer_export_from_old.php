<?php

$pdo = new \PDO(
    'mysql:host=mysql;dbname=admin_ogloszenia', 'root', '', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
]
);

$stmt = $pdo->prepare(
    /** @lang MySQL */ "
    SELECT 
            o_ogloszenia.id AS listing_id,
            o_ogloszenia.user_id AS listing_user_id,
            o_ogloszenia.user_id AS listing_user_id_legacy,
            
            o_ogloszenia.tytul AS listing_title,
            o_ogloszenia.opis_d AS listing_description,
            o_ogloszenia.cena AS listing_price_legacy,
            o_ogloszenia.telefon AS listing_phone,
            o_ogloszenia.mail AS listing_email,
            o_ogloszenia.miejscowosc AS listing_city,
            o_ogloszenia.podkat AS listing_category_legacy,
           
            o_ogloszenia.poziom AS listing_level_legacy,
            o_ogloszenia.akceptacja AS listing_admin_confirmed,
            o_ogloszenia.bDeleted AS listing_user_deleted,
            o_ogloszenia.dokiedy AS listing_valid_until_date,
            o_ogloszenia.premium AS listing_featured,
            o_ogloszenia.dokiedy_premium AS listing_featured_until_date,
            o_ogloszenia.data AS listing_first_created_date,
            o_ogloszenia.data_ostatnia_modyfikacja AS listing_last_edit_date,
            o_ogloszenia.data_ostatnia_aktywacja AS listing_last_confirm,
            o_ogloszenia.powod_odrzucenia AS listing_rejection_reason,
           
            o_ogloszenia.odwiedziny AS listing_views_count,
            o_ogloszenia.d_ip AS ip_legacy,
           
           o_galeria.kolejnosc AS listing_file_sort,
           o_galeria.img AS listing_file_path_legacy,
           
           o_uzytkownicy.id AS listing_user_id,
           o_uzytkownicy.login AS listing_user_name,
           o_uzytkownicy.mail AS listing_user_email,
           o_uzytkownicy.haslo AS listing_user_pass,
           o_uzytkownicy.data AS listing_user_registration_date,
           o_uzytkownicy.ostatnie_logowanie AS listing_user_last_login,
        
           null
    FROM o_ogloszenia 
        LEFT JOIN o_uzytkownicy ON (o_uzytkownicy.id = o_ogloszenia.user_id)
        LEFT JOIN o_galeria ON (o_galeria.o_id = o_ogloszenia.id)
    WHERE 
        1
        && o_ogloszenia.bDeleted=0
        && o_uzytkownicy.poziom=1
ORDER BY 
#    o_ogloszenia.id DESC,
    o_ogloszenia.id ASC,
    o_uzytkownicy.id ASC,
    o_galeria.id ASC
# LIMIT 100
");
$stmt->execute();

$fpCsv = fopen($argv[1], 'w');
$header = [
    'listing_id',

    'listing_title',
    'listing_description',
    'listing_price',
    'listing_phone',
    'listing_email',
    'listing_city',
    'listing_category',

    'listing_admin_confirmed',
    'listing_user_deleted',
    'listing_valid_until_date',
    'listing_featured',
    'listing_featured_until_date',
    'listing_first_created_date',
    'listing_last_edit_date',

    'listing_views_count',
    'listing_police_log',
    'listing_featured_weight',
    'listing_admin_removed',
    'listing_user_deactivated',
    'listing_rejection_reason',
    'listing_admin_rejected',
    'listing_email_show',
    'listing_order_by_date',
    'listing_slug',

    'listing_file_sort',
    'listing_file_path',
    'listing_file_user_removed',
    'listing_file_filename',
    'listing_file_mime_type',
    'listing_file_size_bytes',

    'listing_user_id',
    'listing_user_name',
    'listing_user_email',
    'listing_user_pass',
    'listing_user_registration_date',
    'listing_user_last_login',
];

if (count($header) !== count(array_unique($header))) {
    echo "header has some duplicates, fix it\r\n";
    exit;
}

fputcsv($fpCsv, $header);
while ($dbRow = $stmt->fetch(\PDO::FETCH_ASSOC)) {

    $dbRow['listing_category'] = mapCategory($dbRow);
    $dbRow['listing_price'] = normalizePrice($dbRow['listing_price_legacy']);
    if (!empty(trim($dbRow['listing_price_legacy']))) {
        $dbRow['listing_description'] .= "\r\n\r\nCena: " . $dbRow['listing_price_legacy'];
    }

    $dbRow['listing_admin_rejected'] = 0;
    if ($dbRow['listing_admin_confirmed'] === -1) {
        $dbRow['listing_admin_confirmed'] = 0;
        $dbRow['listing_admin_rejected'] = 1;
    }

    $dbRow = setUserDeactivated($dbRow);

    if ($dbRow['listing_featured_until_date'] < '2000-00-00 00:00:00') {
        $dbRow['listing_featured_until_date'] = null;
    }

    if ($dbRow['listing_last_edit_date'] < '2000-00-00 00:00:00') {
        $dbRow['listing_last_edit_date'] = $dbRow['listing_first_created_date'];
    }

    if ($dbRow['listing_first_created_date'] < '2000-00-00 00:00:00') {
        $dbRow['listing_first_created_date'] = $dbRow['listing_last_edit_date'];
    }

    if ($dbRow['listing_valid_until_date'] < '2000-00-00 00:00:00') {
        $dbRow['listing_valid_until_date'] = $dbRow['listing_first_created_date'];
    }

    if ($dbRow['listing_user_last_login'] < '2000-00-00 00:00:00') {
        $dbRow['listing_user_last_login'] = null;
    }

    $dbRow['listing_police_log'] = " | {$dbRow['ip_legacy']} {$dbRow['listing_first_created_date']} | ";

    // not present set default
    $dbRow['listing_featured_weight'] = 0;
    $dbRow['listing_admin_removed'] = 0;
    $dbRow['listing_email_show'] = 1;
    $dbRow['listing_order_by_date'] = $dbRow['listing_last_edit_date'];
    $dbRow['listing_slug'] = $dbRow['listing_id'] . '-ogloszenia-jaslo';

    // file
    $dbRow['listing_file_path'] = getNewImagePath($dbRow);
    $dbRow['listing_file_user_removed'] = 0;
    $dbRow['listing_file_filename'] = basename($dbRow['listing_file_path']);
    $dbRow['listing_file_mime_type'] = 'image/jpg';
    $dbRow['listing_file_size_bytes'] = 1024*150;

    $csvRow = [];
    foreach ($header as $headerValue) {
        if (!array_key_exists($headerValue, $dbRow)) {
            echo $headerValue.' ';
            continue;
        }
        $csvRow[$headerValue] = $dbRow[$headerValue];
    }

    if (count($csvRow) !== count($header)) {
        print_r($csvRow) . "\r\n";
        exit;
    }

    fputcsv($fpCsv, $csvRow);
}

fclose($fpCsv);

function getNewImagePath(array $dbRow) {
    if (empty(trim($dbRow['listing_file_path_legacy']))) {
        return '';
    }

    $userId = (int) $dbRow['listing_user_id_legacy'];
    $imgDir = (int) ($userId / 32000);

    $filename = basename($dbRow['listing_file_path_legacy']);
    return "static/listing/0000_legacy/galeria/$imgDir/$userId/s$filename";
}

function setUserDeactivated(array $dbRow) {
    $dbRow['listing_user_deactivated'] = 0;

    if ($dbRow['listing_level_legacy'] === 0) {
        $dbRow['listing_user_deactivated'] = 1;
    }

    if ($dbRow['listing_level_legacy'] === 1) {
        $dbRow['listing_user_deactivated'] = 0;
    }

    return $dbRow;
}

function mapCategory(array $dbRow) {
    $map = [
        71 => 1201,
        72 => 1202,
        73 => 1203,
        110 => 1204,
        63 => 401,
        64 => 405,
        65 => 402,
        66 => 406,
        83 => 404,
        102 => 403,
        104 => 407,
        67 => 1301,
        68 => 1302,
        69 => 1304,
        57 => 1105,
        58 => 1101,
        59 => 1104,
        91 => 1102,
        108 => 1103,
        27 => 101,
        28 => 104,
        29 => 107,
        74 => 103,
        81 => 105,
        105 => 106,
        111 => 102,
        31 => 802,
        32 => 801,
        33 => 804,
        60 => 806,
        82 => 803,
        87 => 805,
        35 => 601,
        36 => 602,
        37 => 603,
        93 => 604,
        94 => 605,
        95 => 606,
        96 => 607,
        97 => 608,
        98 => 609,
        103 => 610,
        38 => 201,
        39 => 205,
        40 => 203,
        90 => 202,
        92 => 204,
        54 => 501,
        55 => 504,
        56 => 507,
        61 => 505,
        84 => 503,
        85 => 502,
        88 => 508,
        89 => 506,
        41 => 701,
        42 => 702,
        43 => 704,
        107 => 703,
        45 => 302,
        46 => 301,
        47 => 305,
        106 => 303,
        109 => 304,
        48 => 902,
        49 => 901,
        78 => 1503,
        79 => 1502,
        80 => 1501,
        101 => 1504,
        51 => 1001,
        52 => 1004,
        70 => 1003,
        86 => 1002,
        75 => 1401,
        76 => 1402,
        77 => 1403,
    ];

    $catId = $dbRow['listing_category_legacy'];
    return $map[$catId] ?? 1204;
}

function normalizePrice(string $price): ?int {
    if (empty(trim($price))) {
        return null;
    }

    if (ctype_digit($price)) {
        return $price;
    }

    $return = $price;
    $return = trim($return);

    $return = str_ireplace('PLN', '', $return);
    $return = str_ireplace('zl', '', $return);
    $return = str_ireplace('zł', '', $return);
    $return = str_ireplace('/', '', $return);
    $return = str_ireplace('szt', '', $return);
    $return = str_ireplace(',-', '', $return);
    $return = str_ireplace('kpl', '', $return);
    $return = str_ireplace('.000', '000', $return);
    $return = str_ireplace(',000', '000', $return);
    $return = str_ireplace(',00', '', $return);
    $return = str_ireplace('.00', '', $return);
    $return = str_ireplace('brutto', '', $return);
    $return = str_ireplace('godz', '', $return);
    $return = str_ireplace('netto', '', $return);
    $return = str_ireplace('msc', '', $return);
    $return = str_ireplace('tona', '', $return);
    $return = str_ireplace('od', '', $return);
    $return = str_ireplace(' ', '', $return);
    $return = trim($return);
    $return = trim($return, '.');

    if (ctype_digit($return) && $return > 1) {
//        echo "success $price -> $return\r\n";
        return $return;
    }

    if (
        stripos($return, 'neg') === false
        && stripos($return, 'uzg') === false
        && stripos($return, 'usta') === false
        && stripos($return, 'atra') === false
        && stripos($return, 'konk') === false
        && stripos($return, 'uzgodnienia') === false
    ) {
        if (!empty($return)) {
//            echo "failed: $return\r\n";
        }
    }

    return null;
}
