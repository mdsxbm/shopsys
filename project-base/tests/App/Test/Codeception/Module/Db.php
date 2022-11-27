<?php

declare(strict_types=1);

namespace Tests\App\Test\Codeception\Module;

use Codeception\Module\Db as BaseDb;
use Shopsys\FrameworkBundle\Component\Doctrine\DatabaseSchemaFacade;
use Tests\App\Test\Codeception\Helper\SymfonyHelper;

class Db extends BaseDb
{
    /**
     * Revert database to the original state
     */
    public function _afterSuite(): void
    {
        $this->_loadDump();
    }

    public function cleanup(): void
    {
        /** @var \Tests\App\Test\Codeception\Helper\SymfonyHelper $symfonyHelper */
        $symfonyHelper = $this->getModule(SymfonyHelper::class);
        /** @var \Shopsys\FrameworkBundle\Component\Doctrine\DatabaseSchemaFacade $databaseSchemaFacade */
        $databaseSchemaFacade = $symfonyHelper->grabServiceFromContainer(DatabaseSchemaFacade::class);
        $databaseSchemaFacade->dropSchemaIfExists('public');
    }

    /**
     * @inheritDoc
     * @param string|null $databaseKey
     * @param mixed[]|null $databaseConfig
     */
    public function _loadDump($databaseKey = null, $databaseConfig = null): void
    {
        $this->cleanup();
        parent::_loadDump($databaseKey, $databaseConfig);
    }
}
