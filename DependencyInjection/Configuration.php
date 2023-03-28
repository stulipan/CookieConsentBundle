<?php

declare(strict_types=1);



namespace Stulipan\CookieConsentBundle\DependencyInjection;

use Stulipan\CookieConsentBundle\Enum\CategoryEnum;
use Stulipan\CookieConsentBundle\Enum\PositionEnum;
use Stulipan\CookieConsentBundle\Enum\ThemeEnum;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('cookie_consent');

        if (method_exists($treeBuilder, 'getRootNode')) {
            $rootNode = $treeBuilder->getRootNode();
        } else {
            // BC layer for symfony/config 4.1 and older
            $rootNode = /* @scrutinizer ignore-deprecated */ $treeBuilder->root('cookie_consent');
        }

        $rootNode
            ->children()
                ->variableNode('categories')
                    ->defaultValue([CategoryEnum::CATEGORY_STATISTICS, CategoryEnum::CATEGORY_PERSONALIZATION, CategoryEnum::CATEGORY_MARKETING])
                ->end()
                ->enumNode('position')
                    ->defaultValue(PositionEnum::POSITION_TOP)
                    ->values(PositionEnum::getAvailablePositions())
                ->end()
                ->booleanNode('use_logger')
                    ->defaultTrue()
                ->end()
                ->booleanNode('simplified')
                    ->defaultFalse()
                ->end()
                ->booleanNode('http_only')
                    ->defaultTrue()
                ->end()
                ->scalarNode('form_action')
                    ->defaultNull()
                ->end()
                ->booleanNode('csrf_protection')
                    ->defaultTrue()
                ->end()
                ->scalarNode('privacy_policy_url')
                    ->defaultNull()
                ->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
