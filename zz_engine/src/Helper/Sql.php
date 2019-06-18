<?php

declare(strict_types=1);

namespace App\Helper;

use Doctrine\Bundle\DoctrineBundle\Twig\DoctrineExtension;
use Doctrine\ORM\Query\Parameter;
use Doctrine\ORM\Query\ParserResult;
use Doctrine\ORM\QueryBuilder;
use ReflectionObject;
use Symfony\Component\VarDumper\Cloner\Data;

class Sql
{
    public static function getParametersFromQb(QueryBuilder $qb): array
    {
        $query = $qb->getQuery();
        $reflector = new ReflectionObject($query);
        $method = $reflector->getMethod('_parse');
        $method->setAccessible(true);
        /** @var ParserResult $parserResult */
        $parserResult = $method->invoke($query);

        $params = [];
        /** @var Parameter[] $doctrineQueryParamList */
        $doctrineQueryParamList = $qb->getParameters()->toArray();
        foreach ($doctrineQueryParamList as $key => $doctrineQueryParam) {
            foreach ($parserResult->getSqlParameterPositions($doctrineQueryParam->getName()) as $sqlParameterPosition) {
                $params[$sqlParameterPosition] = $doctrineQueryParam->getValue();
            }
        }

        return $params;
    }
}
