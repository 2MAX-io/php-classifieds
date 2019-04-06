<?php

declare(strict_types=1);

namespace App\Tests\Smoke;

use App\Helper\JsonHelper;
use App\Service\System\Upgrade\Base\UpgradeApiEnum;
use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Smoke\Base\SmokeTestForRouteInterface;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class LicenseControllerTest extends AppIntegrationTestCase implements SmokeTestForRouteInterface
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public static function getRouteNames(): array
    {
        return ['app_license_show'];
    }

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->executeSql("UPDATE setting SET value = 'test_license' WHERE name = 'license'");

        $url = $this->getRouter()->generate('app_license_show', [
            'urlSecret' => $_ENV['APP_2MAX_URL_SECRET'],
        ]);
        // signature only for http://localhost
        $signature = <<<'EOT'
ULa4nVy3Nj9P3/qNqNqLVyGluCIOJlCYVDgjfzG6CLzSkCFtYHfgBdPcYx3idexZCg+8Yigbj2w2wBTBPbheovQUg2ZoRj667BqFs+GtHQt/tJm7qw9iIvYowhEa57Ar29fQAf/sjlWWeCSSVqBm2aUknYsHwCcrz/+6ZlTIs4KqAy9QLEfvex05o+vtuEDgSVZALJWU1ugPNWEkAWX2xdjg38/owa0Lu3A8TQFYyzkfC8RAJxTX8WdNsd0TfHsA3eM/0LkQh7tzT8CNGc19KPsrmAe3mnIvLy2i49T0HXL9DGCZBTJDpf5ecvZ/b9LZuXY+5cyPB9vE+Jmw7PteZ7znB19/ppkXwA9LmZnpY4KP3B3o/F8WWF4vJHV31zZNgOMAaGWSbyVPu7c5Ebmf/ofLEqgP6wjjGzd3OQa64qFetjitxJpW3eXWiV658TA0kxTTRlWAw/lizVYahjcb1N72Q3si6Vjx1FiIsvY7noRtqK26WSt6go9mv+GH8IK+6l4zwSmDkw2msyNXyFSLVKNVFtyPgV1NgVl3Y4Myz+l9zJf9ZMLAewfLxqvEk9QkN/3WiJ1Q2RJniXKhPR8nyRiM63K1dRVDZkzdpFc3YsioyULYJB1cEZIi5d9Ivy7kqiho6iEIlvxd3ea/9KR5uVLBbpL859w39eMQU75FcJo=
EOT;
        $requestArray = [
            'licenseShowAbsoluteUrl' => 'http://localhost/zzzz/TEST_APP_2MAX_URL_SECRET/license/show-license',
        ];
        $client->request('GET', $url, [], [], [
            'HTTP_'.UpgradeApiEnum::HEADER_SIGNATURE => $signature,
        ],
            \json_encode($requestArray, \JSON_PRETTY_PRINT) ?: 'error while encoding json'
        );
        $response = $client->getResponse();

        $content = (string) $response->getContent();
        self::assertSame(
            200,
            $response->getStatusCode(),
            \print_r(JsonHelper::toArray($content), true)
        );
        $responseArray = JsonHelper::toArray($content);
        self::assertSame('test_license', $responseArray['license']);
    }
}
