<?php

declare(strict_types=1);

namespace App\Twig;

use App\Entity\Category;
use Twig\Extension\RuntimeExtensionInterface;

class TwigAdvert implements RuntimeExtensionInterface
{
    public const DEFAULT_ZONE = 54;
    public const MOTO = 100;
    public const CONSTRUCTION = 1200;
    public const MOTO_PARTS = 107;
    public const MOTO_WHEELS_TIRES = 103;
    public const MOTO_MOTORBIKES = 105;
    public const MOTO_CARS = 101;
    public const MOTO_SERVICES = 104;
    public const HOME_GARDEN = 400;
    public const FINANCES = 1300;
    public const REAL_ESTATE = 800;
    public const CLOTHING = 600;
    public const JOBS = 200;
    public const OTHER = 500;
    public const HOME_ELECTRONIC = 700;
    public const SPORT = 300;
    public const EVENTS = 1500;
    public const PHONES = 900;
    public const OTHER_SERVICES = 1000;
    public const HEALTH_BEAUTY = 1400;

    public function categoryToAdvertZoneId(?Category $category): int
    {
        if (null === $category) {
            return static::DEFAULT_ZONE;
        }

        // category id => zone id
        $categoryToZoneMap = [];
        $categoryToZoneMap[self::MOTO] = 14;
        $categoryToZoneMap[self::MOTO_PARTS] = 34;
        $categoryToZoneMap[self::MOTO_WHEELS_TIRES] = 35;
        $categoryToZoneMap[self::MOTO_MOTORBIKES] = 36;
        $categoryToZoneMap[self::MOTO_CARS] = 33;
        $categoryToZoneMap[self::MOTO_SERVICES] = 32;
        $categoryToZoneMap[self::CONSTRUCTION] = 25;
        $categoryToZoneMap[self::HOME_GARDEN] = 24;
        $categoryToZoneMap[self::FINANCES] = 26;
        $categoryToZoneMap[self::REAL_ESTATE] = 18;
        $categoryToZoneMap[self::CLOTHING] = 16;
        $categoryToZoneMap[self::JOBS] = 22;
        $categoryToZoneMap[self::OTHER] = 15;
        $categoryToZoneMap[self::HOME_ELECTRONIC] = 17;
        $categoryToZoneMap[self::SPORT] = 19;
        $categoryToZoneMap[self::EVENTS] = 28;
        $categoryToZoneMap[self::PHONES] = 20;
        $categoryToZoneMap[self::OTHER_SERVICES] = 21;
        $categoryToZoneMap[self::HEALTH_BEAUTY] = 27;

        return $categoryToZoneMap[$category->getId()] ?? $categoryToZoneMap[$category->getParent()->getId()] ?? static::DEFAULT_ZONE;
    }
}
