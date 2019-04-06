<?php

declare(strict_types=1);

namespace App\Helper;

use Doctrine\DBAL\Statement;

class DbHelper
{
    /**
     * @param array<string,float|int|string> $params
     * @param \PDOStatement|Statement $stmt
     *
     * @return \PDOStatement|Statement
     */
    public static function bindParamsFromArray(array $params, $stmt)
    {
        foreach ($params as $paramName => $paramValue) {
            $stmt->bindValue($paramName, $paramValue);
        }

        return $stmt;
    }
}
