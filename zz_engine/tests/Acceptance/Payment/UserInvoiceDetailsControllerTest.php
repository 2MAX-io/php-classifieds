<?php

declare(strict_types=1);

namespace App\Tests\Acceptance\Payment;

use App\Tests\Base\AppIntegrationTestCase;
use App\Tests\Traits\DatabaseTestTrait;
use App\Tests\Traits\LoginTestTrait;
use App\Tests\Traits\RouterTestTrait;

/**
 * @internal
 */
class UserInvoiceDetailsControllerTest extends AppIntegrationTestCase
{
    use DatabaseTestTrait;
    use RouterTestTrait;
    use LoginTestTrait;

    public function test(): void
    {
        $client = static::createClient();
        $this->clearDatabase();
        $this->loginUser($client);

        $client->request('GET', $this->getRouter()->generate('app_user_invoice_details'));
        $client->submitForm('Save Invoice Details', [
            'user_invoice_details[companyName]' => 'company name',
            'user_invoice_details[taxNumber]' => '1111111',
            'user_invoice_details[firstName]' => 'test first name',
            'user_invoice_details[lastName]' => 'test last name',
            'user_invoice_details[street]' => 'test street',
            'user_invoice_details[buildingNumber]' => '2222',
            'user_invoice_details[unitNumber]' => '333',
            'user_invoice_details[city]' => 'test city',
            'user_invoice_details[zipCode]' => '00-000',
            'user_invoice_details[country]' => 'Poland',
        ]);
        $response = $client->getResponse();
        self::assertSame(302, $response->getStatusCode());
        $client->followRedirect();
        self::assertSame('app_user_invoice_details', $client->getRequest()->attributes->get('_route'));
    }
}
