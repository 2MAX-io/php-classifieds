<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Payment\Paypal;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\MakePaymentPaypalTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class MakeFeaturedTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;
    use MakePaymentPaypalTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $this->makePayPalPayment();
    }
}
