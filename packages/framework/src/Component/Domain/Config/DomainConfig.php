<?php

declare(strict_types=1);

namespace Shopsys\FrameworkBundle\Component\Domain\Config;

use DateTimeZone;

class DomainConfig
{
    public const STYLES_DIRECTORY_DEFAULT = 'common';

    /**
     * @param int $id
     * @param string $url
     * @param string $name
     * @param string $locale
     * @param \DateTimeZone $dateTimeZone
     * @param string $stylesDirectory
     * @param string|null $designId
     */
    public function __construct(
        protected int $id,
        protected string $url,
        protected string $name,
        protected string $locale,
        protected DateTimeZone $dateTimeZone,
        protected string $stylesDirectory = self::STYLES_DIRECTORY_DEFAULT,
        protected ?string $designId = null,
    ) {
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getUrl()
    {
        return $this->url;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return string
     */
    public function getLocale()
    {
        return $this->locale;
    }

    /**
     * @return string
     */
    public function getStylesDirectory()
    {
        return $this->stylesDirectory;
    }

    /**
     * @return string|null
     */
    public function getDesignId()
    {
        return $this->designId;
    }

    /**
     * @return bool
     */
    public function isHttps()
    {
        return strpos($this->url, 'https://') === 0;
    }

    /**
     * @return \DateTimeZone
     */
    public function getDateTimeZone(): DateTimeZone
    {
        return $this->dateTimeZone;
    }
}
