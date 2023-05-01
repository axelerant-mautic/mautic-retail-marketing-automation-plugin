<?php

declare(strict_types=1);

namespace MauticPlugin\RetailMarketingBundle\Integration;

use Mautic\IntegrationsBundle\Integration\BasicIntegration;
use Mautic\IntegrationsBundle\Integration\Interfaces\BasicInterface;

class RetailMarketingIntegration extends BasicIntegration implements BasicInterface
{
    public const NAME         = 'RetailMarketing';
    public const DISPLAY_NAME = 'Retail Marketing Automation';

    /**
     * {@inheritdoc}
     */
    public function getName(): string
    {
        return self::NAME;
    }

    public function getDisplayName(): string
    {
        return self::DISPLAY_NAME;
    }

    public function getIcon(): string
    {
        return 'plugins/RetailMarketingBundle/Assets/img/icon.png';
    }
}