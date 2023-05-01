<?php

declare(strict_types=1);

use MauticPlugin\RetailMarketingBundle\Integration\RetailMarketingIntegration;
use MauticPlugin\RetailMarketingBundle\Integration\Support\ConfigSupport;

return [
    'name'        => 'Retail Marketing Automation',
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
    ],
];
