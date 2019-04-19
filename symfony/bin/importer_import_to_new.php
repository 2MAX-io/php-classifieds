<?php

$pdo = new \PDO(
    'mysql:host=mysql;dbname=classifieds', 'root', '', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
]
);

$csvHandle = fopen("importer_export_from_old.csv", "r");
$sqlHandle = fopen("importer_import_to_new.sql", "w");

$csvRowNumber = -1;
$header = [];
while (($csvRow = fgetcsv($csvHandle, 0, ",")) !== FALSE) {
    $csvRowNumber++;
    if ($csvRowNumber === 0) {
        $header = $csvRow;
        continue;
    }
    $csvRow = array_combine($header, $csvRow);

    $csvRow['listing_price'] = 0; // todo: make production
    $csvRow['listing_category'] = 3; // todo: make production
    $csvRow['listing_user_id'] = 66; // todo: make production

    $csvRow['listing_search_text'] = $csvRow['listing_title'] . ' ' . $csvRow['listing_description']; // default value, should be regenerated

    $sql = /** @lang MySQL */
        'INSERT INTO listing SET '.arrayToSetString($csvRow).';';

    saveSql($sql, $sqlHandle);
}
fclose($csvHandle);
fclose($sqlHandle);


function saveSql($sql, $sqlHandle) {
    fwrite($sqlHandle, $sql . "\r\n");
}

function escape($unescaped): string {
    $replacements = array(
        "\x00"=>'\x00',
        "\n"=>'\n',
        "\r"=>'\r',
        "\\"=>'\\\\',
        "'"=>"\'",
        '"'=>'\"',
        "\x1a"=>'\x1a'
    );
    return strtr($unescaped,$replacements);
}

function arrayToSetString(array $csvRow): string {
    $csvRow = array_map('escape', $csvRow);
    $map = [
        'listing_title' => 'listing.title',
        'listing_user_id' => 'listing.user_id',
        'listing_description' => 'listing.description',
        'listing_price' => 'listing.price',
        'listing_phone' => 'listing.phone',
        'listing_email' => 'listing.email',
        'listing_city' => 'listing.city',
        'listing_category' => 'listing.category_id',

        'listing_admin_confirmed' => 'listing.admin_confirmed',
        'listing_user_deleted' => 'listing.user_removed',
        'listing_valid_until_date' => 'listing.valid_until_date',
        'listing_featured' => 'listing.featured',
        'listing_featured_until_date' => 'listing.featured_until_date',
        'listing_first_created_date' => 'listing.first_created_date',
        'listing_last_edit_date' => 'listing.last_edit_date',

        'listing_featured_weight' => 'listing.featured_weight',
        'listing_user_deactivated' => 'listing.user_deactivated',
        'listing_admin_removed' => 'listing.admin_removed',
        'listing_admin_rejected' => 'listing.admin_rejected',
        'listing_order_by_date' => 'listing.order_by_date',
        'listing_slug' => 'listing.slug',
        'listing_email_show' => 'listing.email_show',
        'listing_search_text' => 'listing.search_text',
    ];

    $result = '';
    foreach ($csvRow as $key => $value) {
        if (!isset($map[$key])) {
            continue;
        }

        $targetKey = $map[$key];
        $result .= " $targetKey = '$value', ";
    }

    return rtrim($result, ', ');
}
