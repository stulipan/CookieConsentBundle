<?php

declare(strict_types=1);

namespace Stulipan\CookieConsentBundle\Enum;

class CookieNameEnum
{
    const COOKIE_CONSENT_NAME = 'CConsent_Date';

    const COOKIE_CONSENT_KEY_NAME = 'CConsent_Key';

    const COOKIE_CATEGORY_NAME_PREFIX = 'CCategory_';

    /**
     * Get cookie category name.
     */
    public static function getCookieCategoryName(string $category): string
    {
        return self::COOKIE_CATEGORY_NAME_PREFIX.$category;
    }
}
