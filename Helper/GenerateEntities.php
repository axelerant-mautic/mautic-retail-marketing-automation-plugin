<?php

declare(strict_types=1);

namespace MauticPlugin\RetailMarketingBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\LeadBundle\Entity\LeadList;
use Mautic\UserBundle\Entity\User;
use MauticPlugin\CustomObjectsBundle\Entity\CustomField;
use MauticPlugin\CustomObjectsBundle\Entity\CustomObject;

final class GenerateEntities
{
    private EntityManagerInterface $entityManager;
    private User $adminUser;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->adminUser = $this->entityManager->getReference(User::class, 1);
    }

    public function loadDefaults(): void
    {
        // Custom Object
        $abandonedProduct = $this->loadCustomObject();
        // Segments
        list($primaryList, $reminderList) = $this->loadSegments($abandonedProduct);
    }

    private function loadCustomObject(): CustomObject
    {
        // Create Custom Object
        $abandonedProduct = new CustomObject();
        $abandonedProduct->setAlias('abandoned_product');
        $abandonedProduct->setNameSingular('Abandoned Product');
        $abandonedProduct->setNamePlural('Abandoned Products');
        $abandonedProduct->setDescription('Hold details about Abandoned Products');
        $abandonedProduct->setCreatedByUser($this->adminUser);

        $this->entityManager->persist($abandonedProduct);

        // Create Custom Object fields
        $this->createCustomField($abandonedProduct, [
            'alias' => 'description',
            'label' => 'Description',
            'type'  => 'textarea',
        ]);

        $this->createCustomField($abandonedProduct, [
            'alias' => 'link',
            'label' => 'Link',
            'type'  => 'url',
        ]);

        $this->createCustomField($abandonedProduct, [
            'alias' => 'thumbnail',
            'label' => 'Thumbnail Image',
            'type'  => 'url',
        ]);

        $this->createCustomField($abandonedProduct, [
            'alias' => 'sku',
            'label' => 'SKU',
            'type'  => 'text',
        ]);

        $this->createCustomField($abandonedProduct, [
            'alias' => 'quantity',
            'label' => 'Quantity',
            'type'  => 'text',
        ]);

        $this->createCustomField($abandonedProduct, [
            'alias' => 'price',
            'label' => 'Price',
            'type'  => 'text',
        ]);

        $this->entityManager->flush();
        $this->entityManager->clear();

        return $abandonedProduct;
    }

    /**
     * @param array<string, string> $properties
     */
    private function createCustomField(CustomObject $abandonedProduct, array $properties): void
    {
        $cf = new CustomField();
        $cf->setAlias($properties['alias']);
        $cf->setIsPublished(true);
        $cf->setCustomObject($abandonedProduct);
        $cf->setLabel($properties['label']);
        $cf->setType($properties['type']);
        $cf->setRequired(false);
        $cf->setCreatedByUser($this->adminUser);
        $this->entityManager->persist($cf);
    }

    /**
     * @return LeadList[]
     */
    private function loadSegments(CustomObject $abandonedProduct): array
    {
        $segmentDetails = [
            'name'    => 'Abandoned Card Contacts',
            'alias'   => 'abandoned_card_contacts',
            'filters' => [
                [
                    'glue'       => 'and',
                    'field'      => 'cmo_'.$abandonedProduct->getId(), // Name field of CO.
                    'object'     => 'custom_object',
                    'type'       => 'text',
                    'operator'   => '!empty',
                    'properties' => [
                        'filter'  => null,
                        'display' => null,
                    ],
                ],
            ],
        ];

        $primarySegment  = $this->createSegment($segmentDetails);
        $reminderSeg     = $this->createSegment([
            'name'    => 'Abandoned Card Reminder',
            'alias'   => 'abandoned_card_contacts_reminder',
        ]);

        $this->entityManager->clear();

        return [$primarySegment, $reminderSeg];
    }

    /**
     * @param array<string, mixed> $segmentDetails
     */
    private function createSegment(array $segmentDetails): LeadList
    {
        $segment = new LeadList();
        $segment->setName($segmentDetails['name']);
        $segment->setPublicName($segmentDetails['name']);
        $segment->setAlias($segmentDetails['alias']);
        $segment->setCreatedBy($this->adminUser);

        if (!empty($segmentDetails['filters'])) {
            $segment->setFilters($segmentDetails['filters']);
        }

        $this->entityManager->persist($segment);
        $this->entityManager->flush();

        return $segment;
    }
}
