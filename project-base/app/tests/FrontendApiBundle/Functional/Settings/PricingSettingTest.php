<?php

declare(strict_types=1);

namespace Tests\FrontendApiBundle\Functional\Settings;

use App\DataFixtures\Demo\SettingValueDataFixture;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrontendApiBundle\Component\Price\MoneyFormatterHelper;
use Tests\FrontendApiBundle\Test\GraphQlTestCase;

class PricingSettingTest extends GraphQlTestCase
{
    public function testGetPricingSettings(): void
    {
        $graphQlType = 'settings';
        $response = $this->getResponseContentForGql(__DIR__ . '/graphql/PricingSettingsQuery.graphql');
        $responseData = $this->getResponseDataForGraphQlType($response, $graphQlType);
        $data = $responseData['pricing'];

        $firstDomainCurrency = $this->getFirstDomainCurrency();

        self::assertEquals($firstDomainCurrency->getCode(), $data['defaultCurrencyCode']);
        self::assertEquals($firstDomainCurrency->getMinFractionDigits(), $data['minimumFractionDigits']);
        self::assertEquals(MoneyFormatterHelper::formatWithMaxFractionDigits(Money::create(SettingValueDataFixture::FREE_TRANSPORT_AND_PAYMENT_LIMIT)), $data['freeTransportAndPaymentPriceWithVatLimit']);
    }
}
