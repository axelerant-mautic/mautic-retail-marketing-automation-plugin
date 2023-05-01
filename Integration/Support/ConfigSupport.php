<?php

declare(strict_types=1);

namespace MauticPlugin\RetailMarketingBundle\Integration\Support;

use Mautic\IntegrationsBundle\Integration\DefaultConfigFormTrait;
use Mautic\IntegrationsBundle\Integration\Interfaces\ConfigFormInterface;
use MauticPlugin\RetailMarketingBundle\Integration\RetailMarketingIntegration;

final class ConfigSupport extends RetailMarketingIntegration implements ConfigFormInterface
{
    use DefaultConfigFormTrait;
}
