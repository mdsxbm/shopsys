<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Product\Listing;

class ProductListOrderingConfig
{
    public const ORDER_BY_PRIORITY = 'priority';
    public const ORDER_BY_PRICE_DESC = 'price_desc';
    public const ORDER_BY_PRICE_ASC = 'price_asc';
    public const ORDER_BY_NAME_DESC = 'name_desc';
    public const ORDER_BY_RELEVANCE = 'relevance';
    public const ORDER_BY_NAME_ASC = 'name_asc';

    /**
     * @var string[]
     */
    protected array $supportedOrderingModesNamesById;

    protected string $defaultOrderingModeId;

    protected string $cookieName;

    /**
     * @param string[] $supportedOrderingModesNamesById
     * @param string $defaultOrderingModeId
     * @param string $cookieName
     */
    public function __construct(array $supportedOrderingModesNamesById, string $defaultOrderingModeId, string $cookieName)
    {
        $this->supportedOrderingModesNamesById = $supportedOrderingModesNamesById;
        $this->defaultOrderingModeId = $defaultOrderingModeId;
        $this->cookieName = $cookieName;
    }

    /**
     * @return string[]
     */
    public function getSupportedOrderingModesNamesIndexedById(): array
    {
        return $this->supportedOrderingModesNamesById;
    }

    /**
     * @return string
     */
    public function getCookieName(): string
    {
        return $this->cookieName;
    }

    /**
     * @return string
     */
    public function getDefaultOrderingModeId(): string
    {
        return $this->defaultOrderingModeId;
    }

    /**
     * @return string[]
     */
    public function getSupportedOrderingModeIds(): array
    {
        return array_keys($this->supportedOrderingModesNamesById);
    }
}
