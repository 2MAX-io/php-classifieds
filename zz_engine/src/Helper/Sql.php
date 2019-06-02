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
    public static function replaceParams($query, $parameters)
    {
        if ($parameters instanceof Data) {
            $parameters = $parameters->getValue(true);
        }

        $i = 0;

        if (! \array_key_exists(0, $parameters) && \array_key_exists(1, $parameters)) {
            $i = 1;
        }

        return \preg_replace_callback(
            '/\?|((?<!:):[a-z0-9_]+)/i',
            static function ($matches) use ($parameters, &$i) {
                $key = \substr($matches[0], 1);

                if (! \array_key_exists($i, $parameters) && ($key === false || ! \array_key_exists($key, $parameters))) {
                    return $matches[0];
                }

                $value  = \array_key_exists($i, $parameters) ? $parameters[$i] : $parameters[$key];
                $result = DoctrineExtension::escapeFunction($value);
                $i++;

                return $result;
            },
            $query
        );
    }

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
