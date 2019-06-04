<?php

declare(strict_types=1);

/**
 * php importer_create_sql_for_new_classified.php source_csv.csv target_sql.sql
 *
 * example CSV:
 * listing_id,listing_title,listing_description,listing_price,listing_phone,listing_email,listing_city,listing_category,listing_admin_activated,listing_user_deleted,listing_valid_until_date,listing_featured,listing_featured_until_date,listing_first_created_date,listing_last_edit_date,listing_last_activation,listing_views_count,listing_police_log,listing_ip_legacy,listing_featured_weight,listing_admin_removed,listing_user_deactivated,listing_rejection_reason,listing_admin_rejected,listing_email_show,listing_order_by_date,listing_slug,listing_file_sort,listing_file_path,listing_file_user_removed,listing_file_filename,listing_file_mime_type,listing_file_size_bytes,listing_user_id,listing_user_name,listing_user_email,listing_user_pass,listing_user_registration_date,listing_user_last_login
 */

$pdo = new \PDO(
    'mysql:host=mysql;dbname=classifieds', 'root', '', [
    \PDO::ATTR_ERRMODE => \PDO::ERRMODE_EXCEPTION,
    \PDO::ATTR_EMULATE_PREPARES => false,
    \PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8',
    \PDO::MYSQL_ATTR_USE_BUFFERED_QUERY => false,
]
);

$csvHandle = fopen($argv[1], 'rb');
$sqlHandle = fopen($argv[2], 'wb');
saveSql( /** @lang MySQL */ 'SET NAMES utf8;', $sqlHandle);
saveSql( /** @lang MySQL */ 'SET AUTOCOMMIT = 0;', $sqlHandle);

$header = fgetcsv($csvHandle, 0, ',', '"', "\0");
$currentOldListingId = null;
while (($csvRow = fgetcsv($csvHandle, 0, ',', '"', "\0")) !== FALSE) {
    $csvRow = array_combine($header, $csvRow);

    if ($currentOldListingId !== $csvRow['listing_id']) {
        $currentOldListingId = $csvRow['listing_id'];

        saveListing($csvRow, $sqlHandle);
        saveListingViews($csvRow, $sqlHandle);
        saveUser($csvRow, $sqlHandle);
        savePoliceLog($csvRow, $sqlHandle);
    }
    saveListingFile($csvRow, $sqlHandle);
}


saveSql( /** @lang MySQL */ 'COMMIT;', $sqlHandle);
fclose($csvHandle);
fclose($sqlHandle);


function saveSql($sql, $sqlHandle) {
    fwrite($sqlHandle, $sql . "\r\n");
}

function sqlEscape($unescaped): string {
    // only for use without db connection, when generating large quantity of SQL
    $replacements = array(
        "\x00"=>'\x00',
        "\n"=>'\n',
        "\r"=>'\r',
        "\\"=>'\\\\',
        "'"=>"\'",
        '"'=>'\"',
        "\x1a"=>'\x1a'
    );
    return strtr((string) $unescaped, $replacements);
}

function arrayToSqlSetString(array $csvRow, array $map = null) {
    $csvRow = array_map('sqlEscape', $csvRow);
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
        $result .= arrayToSqlSetStringSingleElement($column, $value);
    }

    return rtrim($result, ', ');
}

function arrayToSqlSetStringSingleElement($column, $value) {
    $notNull = [
        'listing.title',
        'listing.description',
        'user.email',
    ];

    if (\in_array($column, $notNull, true)) {
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

        'listing_admin_activated' => 'listing.admin_activated',
        'listing_user_deleted' => 'listing.user_removed',
        'listing_valid_until_date' => 'listing.valid_until_date',
        'listing_featured' => 'listing.featured',
        'listing_featured_until_date' => 'listing.featured_until_date',
        'listing_first_created_date' => 'listing.first_created_date',
        'listing_last_edit_date' => 'listing.last_edit_date',
        'listing_last_activation' => 'listing.admin_last_activation_date',

        'listing_featured_weight' => 'listing.featured_weight',
        'listing_user_deactivated' => 'listing.user_deactivated',
        'listing_admin_removed' => 'listing.admin_removed',
        'listing_admin_rejected' => 'listing.admin_rejected',
        'listing_order_by_date' => 'listing.order_by_date',
        'listing_slug' => 'listing.slug',
        'listing_email_show' => 'listing.email_show',
        'listing_search_text' => 'listing.search_text',
    ];

    return arrayToSqlSetString($csvRow, $map);
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

    return arrayToSqlSetString($csvRow, $map);
}

function arrayToSetStringUser(array $csvRow): string {
    $map = [
        'listing_user_id' => 'user.id',
        'listing_user_name' => 'user.username',
        'listing_user_email' => 'user.email',
        'listing_user_pass' => 'user.password',
        'listing_user_registration_date' => 'user.registration_date',
        'listing_user_last_login' => 'user.last_login',
        'listing_user_roles' => 'user.roles',
        'listing_user_enabled' => 'user.enabled',
    ];

    return arrayToSqlSetString($csvRow, $map);
}

function saveListingFile(array $csvRow, $sqlHandle) {
    if (empty($csvRow['listing_file_path'])) {
        return;
    }

    $columnsToInclude = [
        'listing_file_listing_id',
        'listing_file_path',
        'listing_file_user_removed',
        'listing_file_filename',
        'listing_file_mime_type',
        'listing_file_size_bytes',
        'listing_file_sort',
    ];

    $row = array_intersect_key($csvRow, array_flip($columnsToInclude));
    $row['listing_file_listing_id'] = $csvRow['listing_id'];

    saveSql( /** @lang MySQL */ 'INSERT INTO listing_file SET '.arrayToSetStringListingFile($row).';', $sqlHandle);
}

function saveListing(array $csvRow, $sqlHandle) {

//    $csvRow['listing_user_id'] = 1; // force user id, to have all listings on single account
    $csvRow['listing_search_text'] = $csvRow['listing_title'] . ' ' . $csvRow['listing_description']; // default value, should be regenerated

    saveSql( /** @lang MySQL */ 'INSERT INTO listing SET '.arrayToSetStringListing($csvRow).';', $sqlHandle);
}

function saveUser(array $csvRow, $sqlHandle) {
    $columnsToInclude = [
        'listing_user_id',
        'listing_user_name',
        'listing_user_email',
        'listing_user_pass',
        'listing_user_registration_date',
        'listing_user_last_login',
        'listing_user_roles',
        'listing_user_enabled',
    ];

    $row = array_intersect_key($csvRow, array_flip($columnsToInclude));
    $row['listing_user_roles'] = '["ROLE_USER"]';
    $row['listing_user_enabled'] = 1;

    saveSql( /** @lang MySQL */ 'REPLACE INTO `user` SET '.arrayToSetStringUser($row).';', $sqlHandle);
}

function saveListingViews(array $csvRow, $sqlHandle) {
    $fileRow = [
        'listing_view.listing_id' => $csvRow['listing_id'],
        'listing_view.view_count' => (int) $csvRow['listing_views_count'],
        'listing_view.datetime' => date('Y') . '-01-01 00:00:00',
    ];

    saveSql( /** @lang MySQL */ 'INSERT INTO listing_view SET '.arrayToSqlSetString($fileRow).';', $sqlHandle);
}

function savePoliceLog(array $csvRow, $sqlHandle) {
    $fileRow = [
        'zzzz_listing_police_log.text' => $csvRow['listing_police_log'],
        'zzzz_listing_police_log.source_ip' => $csvRow['listing_ip_legacy'],
        'zzzz_listing_police_log.destination_ip' => '0.0.0.0',
        'zzzz_listing_police_log.datetime' => $csvRow['listing_first_created_date'],
        'zzzz_listing_police_log.listing_id' => $csvRow['listing_id'],
        'zzzz_listing_police_log.user_id' => $csvRow['user_id'],
    ];

    saveSql( /** @lang MySQL */ 'INSERT INTO zzzz_listing_police_log SET '.arrayToSqlSetString($fileRow).';', $sqlHandle);
}
