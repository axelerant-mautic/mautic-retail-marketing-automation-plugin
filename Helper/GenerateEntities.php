<?php

declare(strict_types=1);

namespace MauticPlugin\RetailMarketingBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use MauticPlugin\CustomObjectsBundle\Entity\CustomField;
use MauticPlugin\CustomObjectsBundle\Entity\CustomObject;

final class GenerateEntities
{
    private EntityManagerInterface $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    public function createCustomObjectAndFields(): void
    {
        // Create Custom Object
        $abandonedProduct = new CustomObject();
        $abandonedProduct->setAlias('abandoned_product');
        $abandonedProduct->setNameSingular('Abandoned Product');
        $abandonedProduct->setNamePlural('Abandoned Products');
        $abandonedProduct->setDescription('Hold details about Abandoned Products');

        $this->entityManager->persist($abandonedProduct);

        // Create Custom Object fields
        $cfDesc = new CustomField();
        $cfDesc->setAlias('description');
        $cfDesc->setIsPublished(true);
        $cfDesc->setCustomObject($abandonedProduct);
        $cfDesc->setLabel('Description');
        $cfDesc->setType('textarea');
        $cfDesc->setRequired(false);
        $this->entityManager->persist($cfDesc);

        $cfProductLink = new CustomField();
        $cfProductLink->setAlias('link');
        $cfProductLink->setIsPublished(true);
        $cfProductLink->setCustomObject($abandonedProduct);
        $cfProductLink->setLabel('Link');
        $cfProductLink->setType('url');
        $cfProductLink->setRequired(false);
        $this->entityManager->persist($cfProductLink);

        $cfImage = new CustomField();
        $cfImage->setAlias('thumbnail');
        $cfImage->setIsPublished(true);
        $cfImage->setCustomObject($abandonedProduct);
        $cfImage->setLabel('Thumbnail Image');
        $cfImage->setType('url');
        $cfImage->setRequired(false);
        $this->entityManager->persist($cfImage);

        $cfSku = new CustomField();
        $cfSku->setAlias('sku');
        $cfSku->setIsPublished(true);
        $cfSku->setCustomObject($abandonedProduct);
        $cfSku->setLabel('SKU');
        $cfSku->setType('text');
        $cfSku->setRequired(false);
        $this->entityManager->persist($cfSku);

        $cfQuantity = new CustomField();
        $cfQuantity->setAlias('quantity');
        $cfQuantity->setIsPublished(true);
        $cfQuantity->setCustomObject($abandonedProduct);
        $cfQuantity->setLabel('Quantity');
        $cfQuantity->setType('text');
        $cfQuantity->setRequired(false);
        $this->entityManager->persist($cfQuantity);

        $cfPrice = new CustomField();
        $cfPrice->setAlias('price');
        $cfPrice->setIsPublished(true);
        $cfPrice->setCustomObject($abandonedProduct);
        $cfPrice->setLabel('Price');
        $cfPrice->setType('text');
        $cfPrice->setRequired(false);
        $this->entityManager->persist($cfPrice);

        $this->entityManager->flush();
        $this->entityManager->clear();
    }
}
