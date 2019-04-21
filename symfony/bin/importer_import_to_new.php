<?php

$pdo = new \PDO(
    'mysql:host=mysql;dbname=classifieds', 'root', '', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
]
);

$csvHandle = fopen($argv[1], "r");
$sqlHandle = fopen($argv[2], "w");
saveSql( /** @lang MySQL */ 'SET NAMES utf8;', $sqlHandle);

$csvRowNumber = -1;
$header = [];
$currentOldListingId = null;
while (($csvRow = fgetcsv($csvHandle, 0, ",")) !== FALSE) {
    $csvRowNumber++;
    if ($csvRowNumber === 0) {
        $header = $csvRow;
        continue;
    }
    $csvRow = array_combine($header, $csvRow);

    $csvRow['listing_id'] = $csvRow['listing_id'] + 1000 * 0; // todo: make production
    $csvRow['listing_user_id'] = 66; // todo: make production

    $csvRow['listing_search_text'] = $csvRow['listing_title'] . ' ' . $csvRow['listing_description']; // default value, should be regenerated

    if ($currentOldListingId !== $csvRow['listing_id']) {
        $currentOldListingId = $csvRow['listing_id'];
        saveSql( /** @lang MySQL */ 'INSERT INTO listing SET '.arrayToSetStringListing($csvRow).';', $sqlHandle);
        saveListingViews($csvRow, $sqlHandle);
    }
    saveGallery($csvRow, $sqlHandle);
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

function arrayToSetString(array $csvRow, array $map = null) {
    $csvRow = array_map('escape', $csvRow);
    $result = '';
    foreach ($csvRow as $key => $value) {
        if ($map === null) {
            $column = $key;
        } else {
            if (!isset($map[$key])) {
                continue;
            }
            $column = $map[$key];
        }
        $result .= arrayToSetStringItem($column, $value);
    }

    return rtrim($result, ', ');
}

function arrayToSetStringItem($column, $value) {
    $notNull = [
        'listing.description',
    ];

    if (in_array($column, $notNull)) {
        return " $column = '$value', ";
    }

    if ($value === null) {
        return " $column = NULL, ";
    }

    if ($value === '') {
        return " $column = NULL, ";
    }

    return " $column = '$value', ";
}

function arrayToSetStringListing(array $csvRow): string {
    $map = [
        'listing_id' => 'listing.id',
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

    return arrayToSetString($csvRow, $map);
}

function arrayToSetStringListingFile(array $csvRow): string {
    $map = [
        'listing_file_listing_id' => 'listing_file.listing_id',
        'listing_file_path' => 'listing_file.path',
        'listing_file_sort' => 'listing_file.sort',
        'listing_file_user_removed' => 'listing_file.user_removed',
        'listing_file_filename' => 'listing_file.filename',
        'listing_file_mime_type' => 'listing_file.mime_type',
        'listing_file_size_bytes' => 'listing_file.size_bytes',
    ];

    return arrayToSetString($csvRow, $map);
}

function saveGallery(array $csvRow, $sqlHandle) {
    if (empty($csvRow['listing_file_path'])) {
        return;
    }

    $csvRow['listing_file_listing_id'] = $csvRow['listing_id'];

    $fileColumns = [
        'listing_file_listing_id',
        'listing_file_path',
        'listing_file_user_removed',
        'listing_file_filename',
        'listing_file_mime_type',
        'listing_file_size_bytes',
        'listing_file_sort',
    ];

    $fileRow = [];
    foreach ($fileColumns as $fileColumn) {
        if (!isset($csvRow[$fileColumn])) {
            continue;
        }
        $fileRow[$fileColumn] = $csvRow[$fileColumn];
    }

    saveSql( /** @lang MySQL */ 'INSERT INTO listing_file SET '.arrayToSetStringListingFile($fileRow).';', $sqlHandle);
}

function saveListingViews(array $csvRow, $sqlHandle) {
    $fileRow = [
        'listing_view.listing_id' => $csvRow['listing_id'],
        'listing_view.view_count' => (int) $csvRow['listing_views_count'],
        'listing_view.datetime' => date('Y') . '-01-01 00:00:00',
    ];

    saveSql( /** @lang MySQL */ 'INSERT INTO listing_view SET '.arrayToSetString($fileRow).';', $sqlHandle);
}
