<?php

declare(strict_types=1);

//$inHandle = fopen($argv[1], "r");
//$outHandle = fopen($argv[2], "w");
//
//$buffor = [];
//
//while ($line = fgets($inHandle)) {
//    $matches = match('#INSERT INTO (\w+) SET (.*)#', $line);
//    $tableName = $matches[1];
//    $setValue = $matches[2];
//
//    time();
//}
//
//fclose($inHandle);
//fclose($outHandle);
//
//
//function match(string $pattern, $subject): ?array {
//    $matches = [];
//    $result = preg_match_all($pattern, $subject, $matches);
//
//    if ($result === false) {
//        throw new \Exception('preg_match error' . \preg_last_error());
//    }
//
//    if ($result !== 1) {
//        return null;
//    }
//
//    return $matches;
//}
//
//function parseSet(string $setString) {
//    foreach ($setString as $letter) {
//
//    }
//}
