<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Model\Cookies;

use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Environment\EnvironmentType;
use Shopsys\FrameworkBundle\Component\Setting\Setting;
use Shopsys\FrameworkBundle\Model\Article\Article;
use Shopsys\FrameworkBundle\Model\Article\ArticleFacade;
use Symfony\Component\HttpFoundation\RequestStack;

class CookiesFacade
{
    public const EU_COOKIES_COOKIE_CONSENT_NAME = 'eu-cookies';

    protected string $environment;

    /**
     * @param string $environment
     * @param \Shopsys\FrameworkBundle\Model\Article\ArticleFacade $articleFacade
     * @param \Shopsys\FrameworkBundle\Component\Setting\Setting $setting
     * @param \Shopsys\FrameworkBundle\Component\Domain\Domain $domain
     * @param \Symfony\Component\HttpFoundation\RequestStack $requestStack
     */
    public function __construct(
        string $environment,
        protected readonly ArticleFacade $articleFacade,
        protected readonly Setting $setting,
        protected readonly Domain $domain,
        protected readonly RequestStack $requestStack,
    ) {
        $this->environment = $environment;
    }

    /**
     * @param int $domainId
     * @return \Shopsys\FrameworkBundle\Model\Article\Article|null
     */
    public function findCookiesArticleByDomainId(int $domainId): ?\Shopsys\FrameworkBundle\Model\Article\Article
    {
        $cookiesArticleId = $this->setting->getForDomain(Setting::COOKIES_ARTICLE_ID, $domainId);

        if ($cookiesArticleId !== null) {
            return $this->articleFacade->findById(
                $this->setting->getForDomain(Setting::COOKIES_ARTICLE_ID, $domainId),
            );
        }

        return null;
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\Article|null $cookiesArticle
     * @param int $domainId
     */
    public function setCookiesArticleOnDomain(?Article $cookiesArticle, $domainId): void
    {
        $cookiesArticleId = null;

        if ($cookiesArticle !== null) {
            $cookiesArticleId = $cookiesArticle->getId();
        }
        $this->setting->setForDomain(
            Setting::COOKIES_ARTICLE_ID,
            $cookiesArticleId,
            $domainId,
        );
    }

    /**
     * @param \Shopsys\FrameworkBundle\Model\Article\Article $article
     * @return bool
     */
    public function isArticleUsedAsCookiesInfo(Article $article): bool
    {
        foreach ($this->domain->getAll() as $domainConfig) {
            if ($this->findCookiesArticleByDomainId($domainConfig->getId()) === $article) {
                return true;
            }
        }

        return false;
    }

    /**
     * @return bool
     */
    public function isCookiesConsentGiven(): bool
    {
        // Cookie fixed bar overlays some elements in viewport and mouseover fails on these elements in acceptance tests.
        if ($this->environment === EnvironmentType::ACCEPTANCE) {
            return true;
        }
        $masterRequest = $this->requestStack->getMainRequest();

        return $masterRequest->cookies->has(static::EU_COOKIES_COOKIE_CONSENT_NAME);
    }
}
