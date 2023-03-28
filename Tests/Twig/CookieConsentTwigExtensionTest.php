<?php

declare(strict_types=1);



namespace Stulipan\CookieConsentBundle\Tests\Twig;

use Stulipan\CookieConsentBundle\Twig\CookieConsentTwigExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Twig\AppVariable;
use Symfony\Component\HttpFoundation\Request;

class CookieConsentTwigExtensionTest extends TestCase
{
    /**
     * @var CookieConsentTwigExtension
     */
    private $chCookieConsentTwigExtension;

    public function setUp(): void
    {
        $this->chCookieConsentTwigExtension = new CookieConsentTwigExtension();
    }

    public function testGetFunctions(): void
    {
        $functions = $this->chCookieConsentTwigExtension->getFunctions();

        $this->assertCount(2, $functions);
        $this->assertSame('cookieConsent_isCookieConsentSavedByUser', $functions[0]->getName());
        $this->assertSame('cookieConsent_isCategoryAllowedByUser', $functions[1]->getName());
    }

    public function testIsCookieConsentSavedByUser(): void
    {
        $request  = new Request();

        $appVariable = $this->createMock(AppVariable::class);
        $appVariable
            ->expects($this->once())
            ->method('getRequest')
            ->wilLReturn($request);

        $context = ['app' => $appVariable];
        $result  = $this->chCookieConsentTwigExtension->isCookieConsentSavedByUser($context);

        $this->assertSame($result, false);
    }

    public function testIsCategoryAllowedByUser(): void
    {
        $request  = new Request();

        $appVariable = $this->createMock(AppVariable::class);
        $appVariable
            ->expects($this->once())
            ->method('getRequest')
            ->wilLReturn($request);

        $context = ['app' => $appVariable];
        $result  = $this->chCookieConsentTwigExtension->isCategoryAllowedByUser($context, 'analytics');

        $this->assertSame($result, false);
    }
}
