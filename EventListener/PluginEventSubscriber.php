<?php

declare(strict_types=1);

namespace MauticPlugin\RetailMarketingBundle\EventListener;

use Doctrine\ORM\NonUniqueResultException;
use Mautic\PluginBundle\Entity\Plugin;
use Mautic\PluginBundle\Event\PluginInstallEvent;
use Mautic\PluginBundle\Model\PluginModel;
use Mautic\PluginBundle\PluginEvents;
use MauticPlugin\RetailMarketingBundle\Exception\PluginNotFoundException;
use MauticPlugin\RetailMarketingBundle\Helper\GenerateEntities;
use MauticPlugin\RetailMarketingBundle\Integration\RetailMarketingIntegration;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

final class PluginEventSubscriber implements EventSubscriberInterface
{
    private PluginModel $pluginModel;
    private GenerateEntities $generateEntities;

    public function __construct(PluginModel $pluginModel, GenerateEntities $generateEntities)
    {
        $this->pluginModel      = $pluginModel;
        $this->generateEntities = $generateEntities;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            PluginEvents::ON_PLUGIN_INSTALL => ['onPluginInstall'],
        ];
    }

    /**
     * @throws NonUniqueResultException
     * @throws PluginNotFoundException
     */
    public function onPluginInstall(PluginInstallEvent $event): void
    {
        if (!$event->checkContext(RetailMarketingIntegration::DISPLAY_NAME)) {
            return;
        }

        // Check if Custom Object Plugin is enabled or not
        $pluginRepo = $this->pluginModel->getRepository();
        /** @var Plugin $customObjects */
        $customObjects = $pluginRepo->findByBundle('CustomObjectsBundle');

        if ($customObjects instanceof Plugin && true !== $customObjects->getIsMissing()) {
            $this->generateEntities->loadDefaults();
        } else {
            throw new PluginNotFoundException('Please add missing CustomObjectsBundle.');
        }
    }
}
