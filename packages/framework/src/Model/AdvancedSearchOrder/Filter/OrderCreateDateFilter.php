<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\AdvancedSearchOrder\Filter;

use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Form\DatePickerType;
use Shopsys\FrameworkBundle\Model\AdvancedSearch\AdvancedSearchFilterInterface;

class OrderCreateDateFilter implements AdvancedSearchFilterInterface
{
    public const NAME = 'orderCreatedAt';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }

    /**
     * {@inheritdoc}
     */
    public function getAllowedOperators(): array
    {
        return [
            self::OPERATOR_AFTER,
            self::OPERATOR_BEFORE,
            self::OPERATOR_IS,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function getValueFormType(): string|\Symfony\Component\Form\FormTypeInterface
    {
        return DatePickerType::class;
    }

    /**
     * {@inheritdoc}
     * @return mixed[]
     */
    public function getValueFormOptions(): array
    {
        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function extendQueryBuilder(QueryBuilder $queryBuilder, $rulesData): void
    {
        foreach ($rulesData as $index => $ruleData) {
            if ($ruleData->value === null) {
                continue;
            }

            /** @var \DateTime $inputDate */
            $inputDate = clone $ruleData->value;

            $parameterName = 'orderCreatedAt_' . $index;
            $parameterName2 = 'orderCreatedAt_' . $index . '_2';

            if ($ruleData->operator === self::OPERATOR_BEFORE) {
                $queryBuilder->andWhere('o.createdAt < :' . $parameterName)
                    ->setParameter($parameterName, $inputDate);
            } elseif ($ruleData->operator === self::OPERATOR_AFTER) {
                $queryBuilder->andWhere('o.createdAt >= :' . $parameterName)
                    ->setParameter($parameterName, $inputDate);
            } elseif ($ruleData->operator === self::OPERATOR_IS) {
                $dateDayAfter = clone $inputDate;
                $dateDayAfter->modify('+1 day');

                $queryBuilder->andWhere('o.createdAt BETWEEN :' . $parameterName . ' AND :' . $parameterName2)
                    ->setParameter($parameterName, $inputDate)
                    ->setParameter($parameterName2, $dateDayAfter);
            }
        }
    }
}
