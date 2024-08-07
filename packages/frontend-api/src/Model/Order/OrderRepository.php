<?php

declare(strict_types=1);

namespace Shopsys\FrontendApiBundle\Model\Order;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Shopsys\FrameworkBundle\Component\String\DatabaseSearching;
use Shopsys\FrameworkBundle\Model\Customer\Customer;
use Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser;
use Shopsys\FrameworkBundle\Model\Order\Order;
use Shopsys\FrontendApiBundle\Model\Resolver\Order\Exception\OrderNotFoundUserError;

class OrderRepository
{
    protected EntityManagerInterface $em;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     */
    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->em = $entityManager;
    }

    /**
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createOrderQueryBuilder()
    {
        return $this->em->createQueryBuilder()
            ->select('o')
            ->from(Order::class, 'o')
            ->where('o.deleted = FALSE');
    }

    /**
     * @param string $uuid
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @return \Shopsys\FrameworkBundle\Model\Order\Order|null
     */
    protected function findByUuidAndCustomerUser(string $uuid, CustomerUser $customerUser)
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.uuid = :uuid')->setParameter(':uuid', $uuid)
            ->andWhere('o.customerUser = :customerUser')->setParameter(':customerUser', $customerUser)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $uuid
     * @param string $urlHash
     * @return \Shopsys\FrameworkBundle\Model\Order\Order|null
     */
    protected function findByUuidAndUrlHash(string $uuid, string $urlHash)
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.uuid = :uuid')->setParameter(':uuid', $uuid)
            ->andWhere('o.urlHash = :urlHash')->setParameter(':urlHash', $urlHash)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $uuid
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function getByUuidAndCustomerUser(string $uuid, CustomerUser $customerUser): Order
    {
        $order = $this->findByUuidAndCustomerUser($uuid, $customerUser);

        if ($order === null) {
            throw new OrderNotFoundUserError(sprintf(
                'Order with UUID \'%s\' not found.',
                $uuid,
            ));
        }

        return $order;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @param int $limit
     * @param int $offset
     * @param \Shopsys\FrontendApiBundle\Model\Order\OrderFilter $filter
     * @return \Shopsys\FrameworkBundle\Model\Order\Order[]
     */
    public function getCustomerUserOrderLimitedList(
        CustomerUser $customerUser,
        int $limit,
        int $offset,
        ?OrderFilter $filter = null,
    ): array {
        $queryBuilder = $this->createCustomerUserOrderLimitedList($customerUser);

        if ($filter) {
            $this->applyOrderFilterToQueryBuilder($filter, $queryBuilder);
        }

        return $queryBuilder
            ->orderBy('o.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @param string $search
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @param int $limit
     * @param int $offset
     * @param \Shopsys\FrontendApiBundle\Model\Order\OrderFilter $filter
     * @return \Shopsys\FrameworkBundle\Model\Order\Order[]
     */
    public function getCustomerUserOrderLimitedSearchList(
        string $search,
        CustomerUser $customerUser,
        int $limit,
        int $offset,
        OrderFilter $filter,
    ): array {
        return $this->createCustomerUserOrderLimitSearchListQueryBuilder($customerUser, $search, $filter)
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @param string $search
     * @param \Shopsys\FrontendApiBundle\Model\Order\OrderFilter $filter
     * @return int
     */
    public function getCustomerUserOrderLimitedSearchListCount(
        CustomerUser $customerUser,
        string $search,
        OrderFilter $filter,
    ): int {
        return $this->createCustomerUserOrderLimitSearchListQueryBuilder($customerUser, $search, $filter)
            ->select('count(o.id)')
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @param \Shopsys\FrontendApiBundle\Model\Order\OrderFilter $filter
     * @return int
     */
    public function getCustomerUserOrderCount(CustomerUser $customerUser, OrderFilter $filter): int
    {
        $queryBuilder = $this->em->createQueryBuilder()
            ->select('count(o.id)')
            ->from(Order::class, 'o')
            ->where('o.deleted = FALSE')
            ->andWhere('o.customerUser = :customerUser')
            ->setParameter('customerUser', $customerUser);

        $this->applyOrderFilterToQueryBuilder($filter, $queryBuilder);

        return $queryBuilder
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $orderNumber
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function getByOrderNumberAndCustomerUser(string $orderNumber, CustomerUser $customerUser): Order
    {
        $order = $this->findByOrderNumberAndCustomerUser($orderNumber, $customerUser);

        if ($order === null) {
            throw new OrderNotFoundUserError(sprintf(
                'Order with order number \'%s\' not found.',
                $orderNumber,
            ));
        }

        return $order;
    }

    /**
     * @param string $orderNumber
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @return \Shopsys\FrameworkBundle\Model\Order\Order|null
     */
    protected function findByOrderNumberAndCustomerUser(string $orderNumber, CustomerUser $customerUser): ?Order
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.number = :orderNumber')->setParameter(':orderNumber', $orderNumber)
            ->andWhere('o.customerUser = :customerUser')->setParameter(':customerUser', $customerUser)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\Customer $customer
     * @param int $limit
     * @param int $offset
     * @return \Shopsys\FrameworkBundle\Model\Order\Order[]
     */
    public function getCustomerOrderLimitedList(Customer $customer, int $limit, int $offset): array
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.customer = :customer')
            ->setParameter('customer', $customer)
            ->orderBy('o.createdAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->getQuery()
            ->execute();
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\Customer $customer
     * @return int
     */
    public function getCustomerOrderCount(Customer $customer): int
    {
        return $this->createOrderQueryBuilder()
            ->select('count(o.id)')
            ->andWhere('o.customer = :customerUser')
            ->setParameter('customerUser', $customer)
            ->getQuery()
            ->getSingleScalarResult();
    }

    /**
     * @param string $uuid
     * @param \Shopsys\FrameworkBundle\Model\Customer\Customer $customer
     * @throws \Doctrine\ORM\NonUniqueResultException
     * @return \Shopsys\FrameworkBundle\Model\Order\Order|null
     */
    protected function findByUuidAndCustomer(string $uuid, Customer $customer): ?Order
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.uuid = :uuid')->setParameter(':uuid', $uuid)
            ->andWhere('o.customer = :customer')->setParameter(':customer', $customer)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $uuid
     * @param \Shopsys\FrameworkBundle\Model\Customer\Customer $customer
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function getByUuidAndCustomer(string $uuid, Customer $customer): Order
    {
        $order = $this->findByUuidAndCustomer($uuid, $customer);

        if ($order === null) {
            throw new OrderNotFoundUserError(sprintf('Order with UUID \'%s\' not found.', $uuid));
        }

        return $order;
    }

    /**
     * @param string $orderNumber
     * @param \Shopsys\FrameworkBundle\Model\Customer\Customer $customer
     * @return \Shopsys\FrameworkBundle\Model\Order\Order|null
     */
    protected function findByOrderNumberAndCustomer(string $orderNumber, Customer $customer): ?Order
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.number = :number')->setParameter(':number', $orderNumber)
            ->andWhere('o.customer = :customer')->setParameter(':customer', $customer)
            ->setMaxResults(1)
            ->getQuery()->getOneOrNullResult();
    }

    /**
     * @param string $orderNumber
     * @param \Shopsys\FrameworkBundle\Model\Customer\Customer $customer
     * @return \Shopsys\FrameworkBundle\Model\Order\Order
     */
    public function getByOrderNumberAndCustomer(string $orderNumber, Customer $customer): Order
    {
        $order = $this->findByOrderNumberAndCustomer($orderNumber, $customer);

        if ($order === null) {
            throw new OrderNotFoundUserError(sprintf('Order with order number \'%s\' not found.', $orderNumber));
        }

        return $order;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createCustomerUserOrderLimitedList(CustomerUser $customerUser): QueryBuilder
    {
        return $this->createOrderQueryBuilder()
            ->andWhere('o.customerUser = :customerUser')
            ->setParameter('customerUser', $customerUser);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Customer\User\CustomerUser $customerUser
     * @param string $search
     * @param \Shopsys\FrontendApiBundle\Model\Order\OrderFilter $filter
     * @return \Doctrine\ORM\QueryBuilder
     */
    protected function createCustomerUserOrderLimitSearchListQueryBuilder(
        CustomerUser $customerUser,
        string $search,
        OrderFilter $filter,
    ): QueryBuilder {
        $queryBuilder = $this->createCustomerUserOrderLimitedList($customerUser);

        $queryBuilder->andWhere('NORMALIZED(o.number) LIKE NORMALIZED(:orderNumber)')
            ->setParameter('orderNumber', DatabaseSearching::getFullTextLikeSearchString($search));

        $this->applyOrderFilterToQueryBuilder($filter, $queryBuilder);

        return $queryBuilder;
    }

    /**
     * @param \Shopsys\FrontendApiBundle\Model\Order\OrderFilter $filter
     * @param \Doctrine\ORM\QueryBuilder $queryBuilder
     */
    protected function applyOrderFilterToQueryBuilder(OrderFilter $filter, QueryBuilder $queryBuilder): void
    {
        if ($filter->getCreatedAfter() !== null) {
            $queryBuilder->andWhere('o.createdAt >= :createdAfter')
                ->setParameter('createdAfter', $filter->getCreatedAfter());
        }

        if ($filter->getStatuses() !== null && count($filter->getStatuses()) > 0) {
            $queryBuilder->andWhere('o.status IN (:statuses)')
                ->setParameter('statuses', $filter->getStatuses());
        }

        if ($filter->getOrderItemsCatnum() === null && $filter->getOrderItemsProductUuid() === null) {
            return;
        }

        $queryBuilder->leftJoin('o.items', 'oi');

        if ($filter->getOrderItemsCatnum() !== null) {
            $queryBuilder->andWhere('oi.catnum = :catnum')
                ->setParameter('catnum', $filter->getOrderItemsCatnum());
        }

        if ($filter->getOrderItemsProductUuid() !== null) {
            $queryBuilder->leftJoin('oi.product', 'p')
                ->andWhere('p.uuid = :productUuid')
                ->setParameter('productUuid', $filter->getOrderItemsProductUuid());
        }
    }
}
