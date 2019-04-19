<?php

$pdo = new \PDO(
    'mysql:host=mysql;dbname=admin_ogloszenia', 'root', '', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
]
);

$stmt = $pdo->prepare("
    SELECT 
            o_ogloszenia.id AS listing_id,
            o_ogloszenia.user_id AS listing_user_id,
            
            o_ogloszenia.tytul AS listing_title,
            o_ogloszenia.opis_d AS listing_description,
            o_ogloszenia.cena AS listing_price,
            o_ogloszenia.telefon AS listing_phone,
            o_ogloszenia.mail AS listing_email,
            o_ogloszenia.miejscowosc AS listing_city,
            o_ogloszenia.podkat AS listing_category_legacy,
           
#          o_ogloszenia.poziom AS listing_,
            o_ogloszenia.akceptacja AS listing_admin_confirmed,
            o_ogloszenia.bDeleted AS listing_user_deleted,
            o_ogloszenia.dokiedy AS listing_valid_until_date,
            o_ogloszenia.premium AS listing_featured,
            o_ogloszenia.dokiedy_premium AS listing_featured_until_date,
            o_ogloszenia.data AS listing_first_created_date,
            o_ogloszenia.data_ostatnia_modyfikacja AS listing_last_edit_date,
            o_ogloszenia.data_ostatnia_aktywacja AS listing_last_confirm,
           
            o_ogloszenia.odwiedziny AS listing_views_count,
            CONCAT(' | ', o_ogloszenia.d_ip, ' ', o_ogloszenia.data, ' | ') AS listing_police_log,
        
           null
    FROM o_ogloszenia 
        LEFT JOIN o_uzytkownicy ON (o_uzytkownicy.id = o_ogloszenia.user_id)
        LEFT JOIN o_galeria ON (o_galeria.o_id = o_ogloszenia.id)
    WHERE o_ogloszenia.bDeleted=0
ORDER BY 
    o_ogloszenia.id DESC,
#    o_ogloszenia.id ASC,
    o_uzytkownicy.id ASC,
    o_galeria.id ASC
LIMIT 100
");
$stmt->execute();

$fpCsv = fopen('importer_export_from_old.csv', 'w');
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
    'listing_admin_rejected',
    'listing_email_show',
    'listing_order_by_date',
    'listing_slug',
];
fputcsv($fpCsv, $header);
while ($dbRow = $stmt->fetch(\PDO::FETCH_ASSOC)) {

    $dbRow['listing_category'] = $dbRow['listing_category_legacy']; // todo mapper
    $dbRow['listing_user_deactivated'] = 0; // todo mapper
    $dbRow['listing_admin_rejected'] = 0; // todo mapper

    // not present set default
    $dbRow['listing_featured_weight'] = 0;
    $dbRow['listing_admin_removed'] = 0;
    $dbRow['listing_email_show'] = 1;
    $dbRow['listing_order_by_date'] = $dbRow['listing_last_edit_date'];
    $dbRow['listing_slug'] = $dbRow['listing_id'];

    $csvRow = [];
    foreach ($header as $headerValue) {
        if (!isset($dbRow[$headerValue])) {
            continue;
        }
        $csvRow[$headerValue] = $dbRow[$headerValue];
    }

    fputcsv($fpCsv, $csvRow);
}

fclose($fpCsv);
