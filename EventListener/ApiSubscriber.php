<?php

namespace MauticPlugin\RetailMarketingBundle\EventListener;

use Mautic\ApiBundle\ApiEvents;
use Mautic\ApiBundle\Event\ApiEntityEvent;
use Mautic\LeadBundle\Entity\Lead;
use MauticPlugin\CustomObjectsBundle\DTO\TableConfig;
use MauticPlugin\CustomObjectsBundle\Entity\CustomFieldValueInterface;
use MauticPlugin\CustomObjectsBundle\Entity\CustomItem;
use MauticPlugin\CustomObjectsBundle\Exception\InvalidArgumentException;
use MauticPlugin\CustomObjectsBundle\Exception\NotFoundException;
use MauticPlugin\CustomObjectsBundle\Model\CustomItemModel;
use MauticPlugin\CustomObjectsBundle\Model\CustomObjectModel;
use MauticPlugin\CustomObjectsBundle\Provider\ConfigProvider;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;

class ApiSubscriber implements EventSubscriberInterface
{
    const LIMIT = 50;
    private ConfigProvider $configProvider;
    private CustomObjectModel $customObjectModel;
    private CustomItemModel $customItemModel;

    public function __construct(
        ConfigProvider $configProvider,
        CustomObjectModel $customObjectModel,
        CustomItemModel $customItemModel
    ) {
        $this->configProvider    = $configProvider;
        $this->customObjectModel = $customObjectModel;
        $this->customItemModel   = $customItemModel;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            ApiEvents::API_ON_ENTITY_PRE_SAVE  => 'unLinkProducts',
        ];
    }

    /**
     * Unlink the Product Custom Items from Contact.
     *
     * @throws NotFoundException
     */
    public function unLinkProducts(ApiEntityEvent $event): void
    {
        try {
            $customObjects = $this->getCustomObjectsFromContactCreateRequest(
                $event->getEntityRequestParameters(),
                $event->getRequest()
            );
        } catch (InvalidArgumentException $e) {
            return;
        }

        /** @var Lead $contact */
        $contact = $event->getEntity();

        if (empty($customObjects) || $contact->isNew()) {
            return;
        }

        $sku = array_map(function ($item) {
            return $item['data'][0]['attributes']['sku'];
        }, $customObjects);

        $products = $this->getCustomItems($contact);

        // Unlink.
        foreach ($products as $product) {
            // Populate the item with the fields
            $product  = $this->customItemModel->fetchEntity($product->getId());
            $skuField = $product->getCustomFieldValues()->filter(
                function (CustomFieldValueInterface $customFieldValue) {
                    return 'sku' === $customFieldValue->getCustomField()->getAlias();
                }
            );

            // Do not unlink product, if they are present and linked.
            if (in_array($skuField->current()->getValue(), $sku)) {
                continue;
            }

            // Unlink the rest.
            $this->customItemModel->unlinkEntity($product, 'contact', (int) $contact->getId());
        }
    }

    /**
     * @param mixed[] $entityRequestParameters
     *
     * @return mixed[]
     *
     * @throws InvalidArgumentException
     */
    private function getCustomObjectsFromContactCreateRequest(array $entityRequestParameters, Request $request): array
    {
        if (!$this->configProvider->pluginIsEnabled()) {
            throw new InvalidArgumentException('Custom Object Plugin is disabled');
        }

        if (1 !== preg_match('/^\/api\/contacts\/.*(new|edit)/', $request->getPathInfo())) {
            throw new InvalidArgumentException('Not a API request we care about');
        }

        if (empty($entityRequestParameters['customObjects']['data']) || !is_array($entityRequestParameters['customObjects']['data'])) {
            throw new InvalidArgumentException('The request payload does not contain any custom items in the customObjects attribute.');
        }

        return array_filter($entityRequestParameters['customObjects']['data'], function ($item) {
            return 'product' === $item['alias'];
        });
    }

    /**
     * @return CustomItem[]
     */
    private function getCustomItems(Lead $contact): array
    {
        $customObject = $this->customObjectModel->getRepository()->findOneBy(['alias' => 'product']);

        $tableConfig = new TableConfig(self::LIMIT, 1, 'CustomItem.id');
        $tableConfig->addParameter('customObjectId', $customObject->getId());
        $tableConfig->addParameter('filterEntityType', 'contact');
        $tableConfig->addParameter('filterEntityId', $contact->getId());

        return $this->customItemModel->getTableData($tableConfig);
    }
}
