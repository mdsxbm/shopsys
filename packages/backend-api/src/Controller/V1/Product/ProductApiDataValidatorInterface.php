<?php

declare(strict_types=1);

namespace Shopsys\BackendApiBundle\Controller\V1\Product;

/**
 * @experimental
 */
interface ProductApiDataValidatorInterface
{
    /**
     * @param mixed[] $productApiData
     * @return string[]
     */
    public function validateCreate(array $productApiData): array;

    /**
     * @param mixed[] $productApiData
     * @return string[]
     */
    public function validateUpdate(array $productApiData): array;
}
