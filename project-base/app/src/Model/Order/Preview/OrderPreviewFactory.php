<?php

declare(strict_types=1);

namespace App\Model\Order\Preview;

use App\Component\Deprecation\DeprecatedMethodException;
use App\Model\Order\PromoCode\PromoCode;
use Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser;
use Shopsys\FrameworkBundle\Model\Order\Preview\OrderPreviewFactory as BaseOrderPreviewFactory;
use Shopsys\FrameworkBundle\Model\Payment\Payment;
use Shopsys\FrameworkBundle\Model\Pricing\Currency\Currency;
use Shopsys\FrameworkBundle\Model\Store\Store;
use Shopsys\FrameworkBundle\Model\Transport\Transport;

/**
 * @property \App\Model\Order\Preview\OrderPreviewCalculation $orderPreviewCalculation
 * @property \App\Model\Order\PromoCode\CurrentPromoCodeFacade $currentPromoCodeFacade
 * @property \App\Model\Cart\CartFacade $cartFacade
 * @property \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
 * @method __construct(\App\Model\Order\Preview\OrderPreviewCalculation $orderPreviewCalculation, \Shopsys\FrameworkBundle\Component\Domain\Domain $domain, \Shopsys\FrameworkBundle\Model\Pricing\Currency\CurrencyFacade $currencyFacade, \App\Model\Customer\User\CurrentCustomerUser $currentCustomerUser, \App\Model\Cart\CartFacade $cartFacade, \App\Model\Order\PromoCode\CurrentPromoCodeFacade $currentPromoCodeFacade)
 * @property \App\Model\Customer\User\CurrentCustomerUser $currentCustomerUser
 */
class OrderPreviewFactory extends BaseOrderPreviewFactory
{
    /**
     * @deprecated use create() method instead
     * @param \App\Model\Transport\Transport|null $transport
     * @param \App\Model\Payment\Payment|null $payment
     * @param \Shopsys\FrameworkBundle\Model\Store\Store|null $personalPickupStore
     * @return \App\Model\Order\Preview\OrderPreview
     */
    public function createForCurrentUser(
        ?Transport $transport = null,
        ?Payment $payment = null,
        ?Store $personalPickupStore = null,
    ): OrderPreview {
        throw new DeprecatedMethodException();
    }
}
