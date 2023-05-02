<?php

declare(strict_types=1);

use MauticPlugin\RetailMarketingBundle\EventListener\PluginEventSubscriber;
use MauticPlugin\RetailMarketingBundle\Helper\GenerateEntities;
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
        ],

        'other' => [
            'retail_marketing.helper.generate_entities' => [
                'class'     => GenerateEntities::class,
                'arguments' => [
                    'doctrine.orm.entity_manager',
                ],
            ],
        ],
    ],
];
