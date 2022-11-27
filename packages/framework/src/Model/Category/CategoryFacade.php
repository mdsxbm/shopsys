<?php

namespace Shopsys\FrameworkBundle\Model\Category;

use Doctrine\ORM\EntityManagerInterface;
use Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Image\ImageFacade;
use Shopsys\FrameworkBundle\Component\Paginator\PaginationResult;
use Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade;
use Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade;
use Shopsys\FrameworkBundle\Model\Category\Exception\CategoryNotFoundException;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Product\Product;

class CategoryFacade
{
    protected const INCREMENT_DUE_TO_MISSING_ROOT_CATEGORY = 1;

    /**
     * @var \Doctrine\ORM\EntityManagerInterface
     */
    protected $em;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryRepository
     */
    protected $categoryRepository;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Domain\Domain
     */
    protected $domain;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryVisibilityRecalculationScheduler
     */
    protected $categoryVisibilityRecalculationScheduler;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade
     */
    protected $friendlyUrlFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Image\ImageFacade
     */
    protected $imageFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade
     */
    protected $pluginCrudExtensionFacade;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryWithPreloadedChildrenFactory
     */
    protected $categoryWithPreloadedChildrenFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryWithLazyLoadedVisibleChildrenFactory
     */
    protected $categoryWithLazyLoadedVisibleChildrenFactory;

    /**
     * @var \Shopsys\FrameworkBundle\Model\Category\CategoryFactoryInterface
     */
    protected $categoryFactory;

    /**
     * @param \Doctrine\ORM\EntityManagerInterface $em
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryRepository $categoryRepository
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryVisibilityRecalculationScheduler $categoryVisibilityRecalculationScheduler
     * @param \Shopsys\FrameworkBundle\Component\Router\FriendlyUrl\FriendlyUrlFacade $friendlyUrlFacade
     * @param \Shopsys\FrameworkBundle\Component\Image\ImageFacade $imageFacade
     * @param \Shopsys\FrameworkBundle\Component\Plugin\PluginCrudExtensionFacade $pluginCrudExtensionFacade
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryWithPreloadedChildrenFactory $categoryWithPreloadedChildrenFactory
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryWithLazyLoadedVisibleChildrenFactory $categoryWithLazyLoadedVisibleChildrenFactory
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryFactoryInterface $categoryFactory
     */
    public function __construct(
        EntityManagerInterface $em,
        CategoryRepository $categoryRepository,
        Domain $domain,
        CategoryVisibilityRecalculationScheduler $categoryVisibilityRecalculationScheduler,
        FriendlyUrlFacade $friendlyUrlFacade,
        ImageFacade $imageFacade,
        PluginCrudExtensionFacade $pluginCrudExtensionFacade,
        CategoryWithPreloadedChildrenFactory $categoryWithPreloadedChildrenFactory,
        CategoryWithLazyLoadedVisibleChildrenFactory $categoryWithLazyLoadedVisibleChildrenFactory,
        CategoryFactoryInterface $categoryFactory
    ) {
        $this->em = $em;
        $this->categoryRepository = $categoryRepository;
        $this->domain = $domain;
        $this->categoryVisibilityRecalculationScheduler = $categoryVisibilityRecalculationScheduler;
        $this->friendlyUrlFacade = $friendlyUrlFacade;
        $this->imageFacade = $imageFacade;
        $this->pluginCrudExtensionFacade = $pluginCrudExtensionFacade;
        $this->categoryWithPreloadedChildrenFactory = $categoryWithPreloadedChildrenFactory;
        $this->categoryWithLazyLoadedVisibleChildrenFactory = $categoryWithLazyLoadedVisibleChildrenFactory;
        $this->categoryFactory = $categoryFactory;
    }

    /**
     * @param int $categoryId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getById(int $categoryId): Category
    {
        return $this->categoryRepository->getById($categoryId);
    }

    /**
     * @param int[] $categoryIds
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getByIds(array $categoryIds): array
    {
        return $this->categoryRepository->getCategoriesByIds($categoryIds);
    }

    /**
     * @param string $categoryUuid
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getByUuid(string $categoryUuid): Category
    {
        return $this->categoryRepository->getOneByUuid($categoryUuid);
    }

    /**
     * @param int $domainId
     * @param string $categoryUuid
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getVisibleOnDomainByUuid(int $domainId, string $categoryUuid): Category
    {
        $category = $this->getByUuid($categoryUuid);
        if (!$category->isVisible($domainId)) {
            throw new CategoryNotFoundException(
                sprintf('Category with UUID "%s" is not visible on domain ID "%s"', $categoryUuid, $domainId)
            );
        }

        return $category;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $categoryData
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function create(CategoryData $categoryData): Category
    {
        $rootCategory = $this->getRootCategory();
        $category = $this->categoryFactory->create($categoryData, $rootCategory);
        $this->em->persist($category);
        $this->em->flush();
        $this->friendlyUrlFacade->createFriendlyUrls('front_product_list', $category->getId(), $category->getNames());
        $this->imageFacade->manageImages($category, $categoryData->image);

        $this->pluginCrudExtensionFacade->saveAllData('category', $category->getId(), $categoryData->pluginData);

        $this->categoryVisibilityRecalculationScheduler->scheduleRecalculationWithoutDependencies();

        return $category;
    }

    /**
     * @param int $categoryId
     * @param \Shopsys\FrameworkBundle\Model\Category\CategoryData $categoryData
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function edit(int $categoryId, CategoryData $categoryData): Category
    {
        $rootCategory = $this->getRootCategory();
        $category = $this->categoryRepository->getById($categoryId);
        $originalNames = $category->getNames();

        $category->edit($categoryData);
        if ($category->getParent() === null) {
            $category->setParent($rootCategory);
        }
        $this->em->flush();
        $this->friendlyUrlFacade->saveUrlListFormData('front_product_list', $category->getId(), $categoryData->urls);
        $this->createFriendlyUrlsWhenRenamed($category, $originalNames);

        $this->imageFacade->manageImages($category, $categoryData->image);

        $this->pluginCrudExtensionFacade->saveAllData('category', $category->getId(), $categoryData->pluginData);

        $this->categoryVisibilityRecalculationScheduler->scheduleRecalculation($category);

        return $category;
    }

    /**
     * @param int $categoryId
     */
    public function deleteById(int $categoryId): void
    {
        $category = $this->categoryRepository->getById($categoryId);

        $this->categoryVisibilityRecalculationScheduler->scheduleRecalculation($category);

        foreach ($category->getChildren() as $child) {
            $child->setParent($category->getParent());
        }
        // Normally, UnitOfWork performs UPDATEs on children after DELETE of main entity.
        // We need to update `parent` attribute of children first.
        $this->em->flush();

        $this->pluginCrudExtensionFacade->removeAllData('category', $category->getId());

        $this->em->remove($category);
        $this->friendlyUrlFacade->removeFriendlyUrlsForAllDomains('front_product_list', $category->getId());
        $this->em->flush();
    }

    /**
     * @param array<int, array{id: string|int, parent_id: string|int|null, depth: int, left: int, right: int}> $categoriesOrderingData
     */
    public function reorderByNestedSetValues(array $categoriesOrderingData): void
    {
        $rootCategoryId = $this->getRootCategory()->getId();

        $query = $this->em->createQuery('
            UPDATE ' . Category::class . ' c 
            SET c.parent = :parent, c.level = :level, c.lft = :lft, c.rgt = :rgt 
            WHERE c.id = :id
        ');

        foreach ($categoriesOrderingData as $categoryOrderingData) {
            $query->execute([
                'id' => (int)$categoryOrderingData['id'],
                'parent' => $categoryOrderingData['parent_id'] ? (int)$categoryOrderingData['parent_id'] : $rootCategoryId,
                'level' => $categoryOrderingData['depth'] + static::INCREMENT_DUE_TO_MISSING_ROOT_CATEGORY,
                'lft' => $categoryOrderingData['left'] + static::INCREMENT_DUE_TO_MISSING_ROOT_CATEGORY,
                'rgt' => $categoryOrderingData['right'] + static::INCREMENT_DUE_TO_MISSING_ROOT_CATEGORY,
            ]);
        }
    }

    /**
     * @return bool
     */
    public function recalculateNestedSet(): bool
    {
        $errors = $this->categoryRepository->verify();

        $this->categoryRepository->recover();
        $this->em->flush();

        return $errors !== true;
    }

    /**
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getAllTranslated(string $locale): array
    {
        return $this->categoryRepository->getAllTranslated($locale);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $selectedCategories
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getAllCategoriesOfCollapsedTree(array $selectedCategories): array
    {
        return $this->categoryRepository->getAllCategoriesOfCollapsedTree($selectedCategories);
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return string[]
     */
    public function getFullPathsIndexedByIdsForDomain(int $domainId, string $locale): array
    {
        return $this->categoryRepository->getFullPathsIndexedByIdsForDomain($domainId, $locale);
    }

    /**
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryWithPreloadedChildren[]
     */
    public function getAllCategoriesWithPreloadedChildren(string $locale): array
    {
        $categories = $this->categoryRepository->getPreOrderTreeTraversalForAllCategories($locale);
        return $this->categoryWithPreloadedChildrenFactory->createCategoriesWithPreloadedChildren($categories);
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryWithPreloadedChildren[]
     */
    public function getVisibleCategoriesWithPreloadedChildrenForDomain(int $domainId, string $locale): array
    {
        $categories = $this->categoryRepository->getPreOrderTreeTraversalForVisibleCategoriesByDomain(
            $domainId,
            $locale
        );

        return $this->categoryWithPreloadedChildrenFactory->createCategoriesWithPreloadedChildren($categories);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getVisibleCategoriesInPathFromRootOnDomain(Category $category, int $domainId): array
    {
        return $this->categoryRepository->getVisibleCategoriesInPathFromRootOnDomain($category, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $parentCategory
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return \Shopsys\FrameworkBundle\Model\Category\CategoryWithLazyLoadedVisibleChildren[]
     */
    public function getCategoriesWithLazyLoadedVisibleChildrenForParent(Category $parentCategory, DomainConfig $domainConfig): array
    {
        $categories = $this->categoryRepository->getTranslatedVisibleSubcategoriesByDomain(
            $parentCategory,
            $domainConfig
        );

        return $this->categoryWithLazyLoadedVisibleChildrenFactory
            ->createCategoriesWithLazyLoadedVisibleChildren($categories, $domainConfig);
    }

    /**
     * @param int $domainId
     * @param string $locale
     * @param string $searchText
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getVisibleByDomainAndSearchText(int $domainId, string $locale, string $searchText): array
    {
        return $this->categoryRepository->getVisibleByDomainIdAndSearchText(
            $domainId,
            $locale,
            $searchText
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getAllVisibleChildrenByCategoryAndDomainId(Category $category, int $domainId): array
    {
        return $this->categoryRepository->getAllVisibleChildrenByCategoryAndDomainId($category, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param string $locale
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]
     */
    public function getAllTranslatedWithoutBranch(Category $category, string $locale): array
    {
        return $this->categoryRepository->getAllTranslatedWithoutBranch($category, $locale);
    }

    /**
     * @param string|null $searchText
     * @param int $limit
     * @return \Shopsys\FrameworkBundle\Component\Paginator\PaginationResult
     */
    public function getSearchAutocompleteCategories(?string $searchText, int $limit): PaginationResult
    {
        $page = 1;

        return $this->categoryRepository->getPaginationResultForSearchVisible(
            $searchText,
            $this->domain->getId(),
            $this->domain->getLocale(),
            $page,
            $limit
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Category\Category[]|null[]
     */
    public function getProductMainCategoriesIndexedByDomainId(Product $product): array
    {
        $mainCategoriesIndexedByDomainId = [];
        foreach ($this->domain->getAll() as $domainConfig) {
            $mainCategoriesIndexedByDomainId[$domainConfig->getId()] = $this->categoryRepository->findProductMainCategoryOnDomain(
                $product,
                $domainConfig->getId()
            );
        }

        return $mainCategoriesIndexedByDomainId;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getProductMainCategoryByDomainId(Product $product, int $domainId): Category
    {
        return $this->categoryRepository->getProductMainCategoryOnDomain($product, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category|null
     */
    public function findProductMainCategoryByDomainId(Product $product, int $domainId): ?Category
    {
        return $this->categoryRepository->findProductMainCategoryOnDomain($product, $domainId);
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @param \Shopsys\FrameworkBundle\Component\Domain\Config\DomainConfig $domainConfig
     * @return string[]
     */
    public function getCategoryNamesInPathFromRootToProductMainCategoryOnDomain(Product $product, DomainConfig $domainConfig): array
    {
        return $this->categoryRepository->getCategoryNamesInPathFromRootToProductMainCategoryOnDomain(
            $product,
            $domainConfig
        );
    }

    /**
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getRootCategory(): Category
    {
        return $this->categoryRepository->getRootCategory();
    }

    /**
     * @param int $domainId
     * @param int $categoryId
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getVisibleOnDomainById(int $domainId, int $categoryId): Category
    {
        $category = $this->getById($categoryId);
        if (!$category->isVisible($domainId)) {
            $message = 'Category ID ' . $categoryId . ' is not visible on domain ID ' . $domainId;
            throw new CategoryNotFoundException($message);
        }

        return $category;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category[] $categories
     * @param \Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup $pricingGroup
     * @param int $domainId
     * @return int[]
     */
    public function getListableProductCountsIndexedByCategoryId(
        array $categories,
        PricingGroup $pricingGroup,
        int $domainId
    ): array {
        return $this->categoryRepository->getListableProductCountsIndexedByCategoryId(
            $categories,
            $pricingGroup,
            $domainId
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param string[] $originalNames
     */
    protected function createFriendlyUrlsWhenRenamed(Category $category, array $originalNames): void
    {
        $changedNames = $this->getChangedNamesByLocale($category, $originalNames);
        if (count($changedNames) === 0) {
            return;
        }

        $this->friendlyUrlFacade->createFriendlyUrls(
            'front_product_list',
            $category->getId(),
            $changedNames
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Category\Category $category
     * @param string[] $originalNames
     * @return string[]
     */
    protected function getChangedNamesByLocale(Category $category, array $originalNames): array
    {
        $changedCategoryNames = [];
        foreach ($category->getNames() as $locale => $name) {
            if ($name !== $originalNames[$locale]) {
                $changedCategoryNames[$locale] = $name;
            }
        }
        return $changedCategoryNames;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Product\Product $product
     * @return \Shopsys\FrameworkBundle\Model\Category\Category
     */
    public function getProductMainCategoryOnCurrentDomain(Product $product): Category
    {
        return $this->getProductMainCategoryByDomainId($product, $this->domain->getId());
    }
}
