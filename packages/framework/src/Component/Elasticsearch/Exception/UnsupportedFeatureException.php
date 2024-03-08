<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Elasticsearch\Exception;

use Exception;

class UnsupportedFeatureException extends Exception
{
    /**
     * @inheritDoc
     */
    public function __construct(string $message)
    {
        parent::__construct($message);
    }
}
