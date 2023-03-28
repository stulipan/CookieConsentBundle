<?php

declare(strict_types=1);



namespace Stulipan\CookieConsentBundle\Tests\DependencyInjection;

use Stulipan\CookieConsentBundle\DependencyInjection\CookieConsentExtension;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Yaml\Parser;

class CookieConsentExtensionTest extends TestCase
{
    /**
     * @var CookieConsentExtension
     */
    private $chCookieConsentExtension;

    /**
     * @var ContainerBuilder
     */
    private $configuration;

    public function setUp(): void
    {
        $this->chCookieConsentExtension = new CookieConsentExtension();
        $this->configuration            = new ContainerBuilder();
    }

    public function testFullConfiguration(): void
    {
        $this->createConfiguration($this->getFullConfig());

        $this->assertParameter(['statistics', 'tracking', 'marketing', 'social_media'], 'cookie_consent.categories');
        $this->assertParameter('top', 'cookie_consent.position');
    }

    /**
     * @expectedException \Symfony\Component\Config\Definition\Exception\InvalidConfigurationException
     */
    public function testInvalidConfiguration(): void
    {
        $this->createConfiguration($this->getInvalidConfig());
    }

    /**
     * create configuration.
     */
    protected function createConfiguration(array $config): void
    {
        $this->chCookieConsentExtension->load([$config], $this->configuration);

        $this->assertTrue($this->configuration instanceof ContainerBuilder);
    }

    /**
     * get full config.
     */
    protected function getFullConfig(): array
    {
        $yaml = <<<EOF
categories: ['analytics', 'tracking', 'marketing', 'social_media']
theme: 'dark'
position: 'top'
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * get invalid config.
     */
    protected function getInvalidConfig(): array
    {
        $yaml = <<<EOF
theme: 'not_existing'
EOF;
        $parser = new Parser();

        return $parser->parse($yaml);
    }

    /**
     * Test if parameter is set.
     */
    private function assertParameter($value, $key): void
    {
        $this->assertSame($value, $this->configuration->getParameter($key), sprintf('%s parameter is correct', $key));
    }
}
