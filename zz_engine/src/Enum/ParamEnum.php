<?php

declare(strict_types=1);

namespace App\Enum;

/**
 * also public on frontend in JS: ParamEnum.js
 */
class ParamEnum
{
    public const DATA_FOR_JS = 'dataForJs';
    public const CSRF_TOKEN = 'csrfToken';
    public const CSRF_HEADER = 'x-csrf-token';
    public const LANGUAGE_ISO = 'languageIso';
    public const COUNTRY_ISO = 'countryIso';
    public const BASE_URL = 'baseUrl';
    public const SUCCESS = 'success';
    public const ERROR = 'error';
    public const USERNAME = 'username';
    public const LISTING_ID = 'listingId';
    public const CATEGORY_ID = 'categoryId';
    public const LISTING_LIST = 'listingList';
    public const LISTING_FILES = 'listingFiles';
    public const CUSTOM_FIELD = 'customField';
    public const PACKAGE_LIST = 'packageList';
    public const SHOW_CONTACT_HTML = 'showContactHtml';
    public const SHOW_LISTING_PREVIEW_FOR_OWNER = 'showListingPreviewForOwner';
    public const PAYMENT_APP_TOKEN = 'paymentAppToken';
    public const POLICE_LOG_TEXT = 'policeLogText';
    public const MAP_LOCATION_COORDINATES = 'mapLocationCoordinates';
    public const MAP_DEFAULT_LATITUDE = 'mapDefaultLatitude';
    public const MAP_DEFAULT_LONGITUDE = 'mapDefaultLongitude';
    public const MAP_DEFAULT_ZOOM = 'mapDefaultZoom';
    public const LONGITUDE = 'longitude';
    public const LATITUDE = 'latitude';
    public const ZOOM = 'zoom';
    public const THOUSAND_SEPARATOR = 'thousandSeparator';
    public const NUMERAL_DECIMAL_MARK = 'numeralDecimalMark';
    public const OBSERVED = 'observed';
}
