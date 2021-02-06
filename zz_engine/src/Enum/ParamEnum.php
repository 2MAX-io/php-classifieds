<?php
declare(strict_types=1);

namespace App\Enum;

/**
 * also public on frontend in JS
 */
class ParamEnum
{
    public const DATA_FOR_JS = 'dataForJs';
    public const MAP_LOCATION_COORDINATES = 'mapLocationCoordinates';
    public const LONGITUDE = 'longitude';
    public const LATITUDE = 'latitude';
    public const ZOOM = 'zoom';
    public const MAP_DEFAULT_LATITUDE = 'mapDefaultLatitude';
    public const MAP_DEFAULT_LONGITUDE = 'mapDefaultLongitude';
    public const MAP_DEFAULT_ZOOM = 'mapDefaultZoom';
    public const LISTING_LIST = 'listingList';
    public const USERNAME = 'username';
    public const CUSTOM_FIELD = 'customField';
}
