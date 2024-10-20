<?php

declare(strict_types=1);

namespace Tests\FrameworkBundle\Unit\Model\AdvancedSearch;

use Doctrine\ORM\QueryBuilder;
use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchQueryBuilderExtender;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchRuleData;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\ProductAdvancedSearchConfig;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\RuleFormViewDataFactory;

class AdvancedSearchQueryBuilderExtenderTest extends TestCase
{
    public function testExtendByAdvancedSearchData()
    {
        $ruleData = new AdvancedSearchRuleData();
        $ruleData->subject = 'testSubject';
        $ruleData->operator = 'testOperator';
        $ruleData->value = 'testValue';

        /** @var \Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchRuleData[] $advancedSearchData */
        $advancedSearchData = [
            RuleFormViewDataFactory::TEMPLATE_RULE_FORM_KEY => null,
            0 => $ruleData,
        ];

        $advancedSearchFilterMock = $this->getMockBuilder(AdvancedSearchFilterInterface::class)->getMock();

        $advancedSearchConfigMock = $this->getMockBuilder(ProductAdvancedSearchConfig::class)
            ->onlyMethods(['getFilter'])
            ->disableOriginalConstructor()
            ->getMock();
        $advancedSearchConfigMock
            ->expects($this->once())
            ->method('getFilter')
            ->with($this->equalTo($ruleData->subject))
            ->willReturn($advancedSearchFilterMock);

        $queryBuilderMock = $this->getMockBuilder(QueryBuilder::class)
            ->onlyMethods([])
            ->disableOriginalConstructor()
            ->getMock();

        $advancedSearchQueryBuilderExtender = new AdvancedSearchQueryBuilderExtender($advancedSearchConfigMock);

        $advancedSearchQueryBuilderExtender->extendByAdvancedSearchData($queryBuilderMock, $advancedSearchData);
    }
}
