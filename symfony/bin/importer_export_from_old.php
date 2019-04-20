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
            o_ogloszenia.cena AS listing_price,
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
            CONCAT(' | ', o_ogloszenia.d_ip, ' ', o_ogloszenia.data, ' | ') AS listing_police_log,
           
           o_galeria.kolejnosc AS listing_file_sort,
           o_galeria.img AS listing_file_path_legacy,
        
           null
    FROM o_ogloszenia 
        LEFT JOIN o_uzytkownicy ON (o_uzytkownicy.id = o_ogloszenia.user_id)
        LEFT JOIN o_galeria ON (o_galeria.o_id = o_ogloszenia.id)
    WHERE o_ogloszenia.bDeleted=0
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
    'listing_user_id',

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
];
fputcsv($fpCsv, $header);
while ($dbRow = $stmt->fetch(\PDO::FETCH_ASSOC)) {

    $dbRow['listing_category'] = $dbRow['listing_category_legacy']; // todo mapper

    $dbRow['listing_admin_rejected'] = 0;
    if ($dbRow['listing_admin_confirmed'] === -1) {
        $dbRow['listing_admin_confirmed'] = 0;
        $dbRow['listing_admin_rejected'] = 1;
    }

    $dbRow = setBasedOnLegacyLevel($dbRow);

    // not present set default
    $dbRow['listing_featured_weight'] = 0;
    $dbRow['listing_admin_removed'] = 0;
    $dbRow['listing_email_show'] = 1;
    $dbRow['listing_order_by_date'] = $dbRow['listing_last_edit_date'];
    $dbRow['listing_slug'] = $dbRow['listing_id'];

    // file
    $dbRow['listing_file_path'] = getOldImagesPath($dbRow);
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
    }

    fputcsv($fpCsv, $csvRow);
}

fclose($fpCsv);

function getOldImagesPath(array $dbRow) {
    if (empty(trim($dbRow['listing_file_path_legacy']))) {
        return '';
    }

    $userId = (int) $dbRow['listing_user_id_legacy'];
    $imgDir = (int) ($userId / 32000);

    $filename = basename($dbRow['listing_file_path_legacy']);
    return "static/user/listing/0000_legacy/images/galeria/$imgDir/$userId/s$filename";
}

function setBasedOnLegacyLevel(array $dbRow) {
    $dbRow['listing_user_deactivated'] = 0;

    if ($dbRow['listing_level_legacy'] === 0) {
        $dbRow['listing_user_deactivated'] = 1;
    }

    if ($dbRow['listing_level_legacy'] === 1) {
        $dbRow['listing_user_deactivated'] = 0;
    }

    return $dbRow;
}
