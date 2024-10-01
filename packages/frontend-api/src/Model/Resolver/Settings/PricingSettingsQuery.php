<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Resolver\Settings;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade;
use Shopsys\FrameworkBundle\Model\TransportAndPayment\FreeTransportAndPaymentFacade;
use Shopsys\FrontendApiBundle\Model\Resolver\AbstractQuery;

class PricingSettingsQuery extends AbstractQuery
{
    /**
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade
     * @param \Shopsys\FrameworkBundle\Model\TransportAndPayment\FreeTransportAndPaymentFacade $freeTransportAndPaymentFacade
     */
    public function __construct(
        protected readonly Domain $domain,
        protected readonly CurrencyFacade $currencyFacade,
        protected readonly FreeTransportAndPaymentFacade $freeTransportAndPaymentFacade,
    ) {
    }

    /**
     * @return array{defaultCurrencyCode: string, minimumFractionDigits: int, freeTransportAndPaymentPriceWithVatLimit: \Shopsys\FrameworkBundle\Component\Money\Money|null}
     */
    public function pricingSettingsQuery(): array
    {
        $domainId = $this->domain->getId();
        $currency = $this->currencyFacade->getDomainDefaultCurrencyByDomainId($domainId);

        return [
            'defaultCurrencyCode' => $currency->getCode(),
            'minimumFractionDigits' => $currency->getMinFractionDigits(),
            'freeTransportAndPaymentPriceWithVatLimit' => $this->freeTransportAndPaymentFacade->isActive($domainId) ? $this->freeTransportAndPaymentFacade->getFreeTransportAndPaymentPriceLimitOnDomain($domainId) : null,
        ];
    }
}
