<?php

declare(strict_types=1);

use MauticPlugin\RetailMarketingBundle\EventListener\ApiSubscriber;
use MauticPlugin\RetailMarketingBundle\EventListener\PluginEventSubscriber;
use MauticPlugin\RetailMarketingBundle\EventListener\TokenSubscriber;
use MauticPlugin\RetailMarketingBundle\Helper\GenerateEntities;
use MauticPlugin\RetailMarketingBundle\Helper\TokenFormatter;
use MauticPlugin\RetailMarketingBundle\Helper\TokenParser;
use MauticPlugin\RetailMarketingBundle\Integration\RetailMarketingIntegration;
use MauticPlugin\RetailMarketingBundle\Integration\Support\ConfigSupport;

return [
    'name'        => RetailMarketingIntegration::DISPLAY_NAME,
    'description' => 'Creates and helps the Retail Marketing Automation flows in Mautic.',
    'version'     => '0.0.1',
    'author'      => 'Axelerant Technologies',

    'services' => [
        'integrations' => [
            // Basic definitions with name, display name and icon.
            'mautic.integration.retailmarketing' => [
                'class' => RetailMarketingIntegration::class,
                'tags'  => [
                    'mautic.integration',
                    'mautic.basic_integration',
                ],
            ],

            // Provides the form types for plugin configuration.
            'retail_marketing.integration.configuration' => [
                'class' => ConfigSupport::class,
                'tags'  => [
                    'mautic.config_integration',
                ],
            ],
        ],

        'events' => [
            'retail_marketing.plugin.event.subscriber' => [
                'class'     => PluginEventSubscriber::class,
                'arguments' => [
                    'mautic.plugin.model.plugin',
                    'retail_marketing.helper.generate_entities',
                ],
            ],
            'retail_marketing.emailtoken.subscriber' => [
                'class'     => TokenSubscriber::class,
                'arguments' => [
                    'mautic.custom.model.object',
                    'mautic.custom.model.item',
                    'mautic.custom.model.field.value',
                    'retail_marketing.token.parser',
                    'retail_marketing.helper.token_formatter',
                ],
            ],
            'retail_marketing.api.subscriber' => [
                'class'     => ApiSubscriber::class,
                'arguments' => [
                    'custom_object.config.provider',
                    'mautic.custom.model.object',
                    'mautic.custom.model.item',
                ],
            ],
        ],

        'other' => [
            'retail_marketing.helper.generate_entities' => [
                'class'     => GenerateEntities::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
            'retail_marketing.token.parser' => [
                'class' => TokenParser::class,
            ],
            'retail_marketing.helper.token_formatter' => [
                'class'     => TokenFormatter::class,
                'arguments' => [
                    'twig',
                ],
            ],
        ],
    ],
];
