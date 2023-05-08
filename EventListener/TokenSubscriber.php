<?php

declare(strict_types=1);

namespace MauticPlugin\RetailMarketingBundle\EventListener;

use Mautic\EmailBundle\EmailEvents;
use Mautic\EmailBundle\Event\EmailBuilderEvent;
use Mautic\EmailBundle\Event\EmailSendEvent;
use MauticPlugin\CustomObjectsBundle\DTO\TableConfig;
use MauticPlugin\CustomObjectsBundle\DTO\Token;
use MauticPlugin\CustomObjectsBundle\Entity\CustomItem;
use MauticPlugin\CustomObjectsBundle\Entity\CustomObject;
use MauticPlugin\CustomObjectsBundle\Exception\NotFoundException;
use MauticPlugin\CustomObjectsBundle\Model\CustomFieldValueModel;
use MauticPlugin\CustomObjectsBundle\Model\CustomItemModel;
use MauticPlugin\CustomObjectsBundle\Model\CustomObjectModel;
use MauticPlugin\RetailMarketingBundle\Helper\TokenFormatter;
use MauticPlugin\RetailMarketingBundle\Helper\TokenParser;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TokenSubscriber implements EventSubscriberInterface
{
    private CustomObjectModel $customObjectModel;
    private CustomItemModel $customItemModel;
    private CustomFieldValueModel $fieldValueModel;
    private TokenParser $tokenParser;
    private TokenFormatter $tokenFormatter;

    public function __construct(
        CustomObjectModel $customObjectModel,
        CustomItemModel $customItemModel,
        CustomFieldValueModel $fieldValueModel,
        TokenParser $tokenParser,
        TokenFormatter $tokenFormatter
    ) {
        $this->customObjectModel = $customObjectModel;
        $this->customItemModel   = $customItemModel;
        $this->fieldValueModel   = $fieldValueModel;
        $this->tokenParser       = $tokenParser;
        $this->tokenFormatter    = $tokenFormatter;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents(): array
    {
        return [
            EmailEvents::EMAIL_ON_BUILD   => ['onBuilderBuild', 0],
            EmailEvents::EMAIL_ON_SEND    => ['decodeTokens', 0],
            EmailEvents::EMAIL_ON_DISPLAY => ['decodeTokens', 0],
        ];
    }

    public function onBuilderBuild(EmailBuilderEvent $event): void
    {
        if (!$event->tokensRequested(TokenParser::TOKEN)) {
            return;
        }
        $customObjects = $this->customObjectModel->fetchAllPublishedEntities();

        foreach ($customObjects as $customObject) {
            $event->addToken(
                $this->tokenParser->buildTokenWithDefaultOptions($customObject->getAlias()),
                $this->tokenParser->buildTokenLabel($customObject->getName())
            );
        }
    }

    public function decodeTokens(EmailSendEvent $event): void
    {
        $tokens = $this->tokenParser->findTokens($event->getContent());

        if (0 === $tokens->count()) {
            return;
        }

        $tokens->map(function (Token $token) use ($event): void {
            try {
                $customObject = $this->customObjectModel->fetchEntityByAlias($token->getCustomObjectAlias());
                $customItems  = $this->getCustomFieldValues($customObject, $token, $event);
            } catch (NotFoundException $e) {
                $customItems = null;
            }

            if (empty($customItems)) {
                return;
            }

            $result = $this->tokenFormatter->format($customItems);

            $event->addToken($token->getToken(), $result);
        });
    }

    /**
     * @return array<int, mixed>
     */
    private function getCustomFieldValues(CustomObject $customObject, Token $token, EmailSendEvent $event): array
    {
        $orderBy  = CustomItem::TABLE_ALIAS.'.id';
        $orderDir = 'DESC';

        $tableConfig = new TableConfig($token->getLimit(), 1, $orderBy, $orderDir);
        $tableConfig->addParameter('customObjectId', $customObject->getId());
        $tableConfig->addParameter('filterEntityType', 'contact');
        $tableConfig->addParameter('filterEntityId', (int) $event->getLead()['id']);
        $tableConfig->addParameter('token', $token);
        $tableConfig->addParameter('email', $event->getEmail());
        $tableConfig->addParameter('source', $event->getSource());
        $customItems = $this->customItemModel->getTableData($tableConfig);
        $fieldValues = [];

        $customItem   = new CustomItem($customObject);
        $customFields = $customItem->getCustomObject()->getPublishedFields();
        $fieldData    = $this->fieldValueModel->getItemsListData($customFields, $customItems);

        foreach ($customItems as $customItem) {
            $fieldValues[$customItem->getId()]['name'] = $customItem->getName();
            foreach ($fieldData->getFields($customItem->getId()) as $field) {
                $fieldValues[$customItem->getId()][$field->getCustomField()->getAlias()] = $field;
            }
        }

        return $fieldValues;
    }
}
