<?php

declare(strict_types=1);

namespace MauticPlugin\RetailMarketingBundle\Helper;

use Doctrine\ORM\EntityManagerInterface;
use Mautic\CampaignBundle\Entity\Campaign;
use Mautic\CampaignBundle\Entity\Event;
use Mautic\EmailBundle\Entity\Email;
use Mautic\LeadBundle\Entity\LeadList;
use Mautic\UserBundle\Entity\User;
use MauticPlugin\CustomObjectsBundle\Entity\CustomField;
use MauticPlugin\CustomObjectsBundle\Entity\CustomFieldOption;
use MauticPlugin\CustomObjectsBundle\Entity\CustomObject;

final class GenerateEntities
{
    const ABANDONED_LABEL = 'Abandoned';
    const ABANDONED_VALUE = 'abandoned';
    const WISHLIST_LABEL  = 'Wishlist';
    const WISHLIST_VALUE  = 'wishlist';
    const REVIEW_LABEL    = 'Review';
    const REVIEW_VALUE    = 'review';
    private EntityManagerInterface $entityManager;
    private User $adminUser;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;

        $this->adminUser = $this->entityManager->getRepository(User::class)->find(1);
    }

    public function loadDefaults(): void
    {
        // Custom Object
        $product = $this->loadCustomObject();

        // Segments
        $segment = $this->loadSegments($product);

        // Email first reminder.
        $html = <<<HTTML
<!DOCTYPE html><head><title>{subject}</title><meta http-equiv="X-UA-Compatible" content="IE=edge"><meta http-equiv="Content-Type" content="text/html; charset=UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1"><link href="https://fonts.googleapis.com/css?family=Open+Sans:300,400,500,700" rel="stylesheet" type="text/css"><style type="text/css">#outlook a{padding:0}.ReadMsgBody{width:100%}.ExternalClass{width:100%}.ExternalClass *{line-height:100%}body{margin:0;padding:0;-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{border-collapse:collapse;mso-table-lspace:0;mso-table-rspace:0}img{border:0;height:auto;line-height:100%;outline:0;text-decoration:none;-ms-interpolation-mode:bicubic}p{display:block;margin:13px 0}@media only screen and (max-width:480px){@-ms-viewport{width:320px}@viewport{width:320px}}body{font-family:"Open Sans",Helvetica,Arial,sans-serif!important;font-size:14px;line-height:1.6;text-align:left;color:#414141}div[data-slot=text]{font-size:14px!important;line-height:1.6!important;text-align:left!important;color:#414141!important;margin-bottom:10px!important}div[style="clear:both"]{margin-bottom:20px!important}.imagecard{background:#eee!important}.imagecard-caption{font-size:12px!important;line-height:1.6!important;text-align:center!important;color:#414141!important;background:#eee!important;padding:10px!important}h1,h2,h3,h4,h5,h6{margin:0!important;margin-bottom:10px!important}.outlook-group-fix{width:100%!important}@media only screen and (min-width:480px){.mj-column-per-100{width:100%!important}}</style></head><body><div data-section-wrapper="1" id="iet9"><div data-section="1" id="iw8y"><table role="presentation" id="irxs" cellspacing="0" cellpadding="0" border="0" align="center"><tbody><tr><td id="i2kk"><div data-slot-container="1" id="irzc" class="mj-column-per-100 outlook-group-fix"><table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" background="#FFFFFF"><tbody><tr></tr></tbody></table></div><table id="ib6g9"><tbody><tr><td id="ihm59"><h1 class="heading">Don't forgot to complete your order!!!</h1><p class="paragraph">{custom-object-list=abandoned_product}</p></td></tr></tbody></table><div data-slot-container="1" id="i7ei1" class="mj-column-per-100 outlook-group-fix"><table role="presentation" id="icwcx" width="100%" cellspacing="0" cellpadding="0" border="0"><tbody><tr><td id="idgqf" align="left"><div data-slot="text" id="isc5z">{unsubscribe_text} | {webview_text}</div></td></tr></tbody></table></div></td></tr></tbody></table></div></div><style>#iet9{background-color:#fff;}#iw8y{Margin:0 auto;border-radius:4px;max-width:600px;}#irxs{width:100%;border-radius:4px;}#i2kk{direction:ltr;padding:20px 0;text-align:center;vertical-align:top;}#irzc{font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;}#i7ei1{font-size:13px;text-align:left;direction:ltr;display:inline-block;vertical-align:top;width:100%;}#icwcx{vertical-align:top;}#idgqf{padding:20px 20px;word-break:break-word;}#isc5z{font-family:'Open Sans',Helvetica,Arial,sans-serif !important;font-size:12px !important;line-height:1.4 !important;text-align:left !important;color:#999 !important;}#ib6g9{height:150px;margin:0 auto 10px auto;padding:5px 5px 5px 5px;width:100%;}#ihm59{padding:0;margin:0;vertical-align:top;}</style></body>
HTTML;
        $email1 = $this->loadEmail([
            'name'    => 'Abandoned Cart First Reminder',
            'subject' => 'Don\'t forgot to complete your order',
            'html'    => $html,
        ]);

        $email2 = $this->loadEmail([
            'name'    => 'Abandoned Cart Second Reminder',
            'subject' => '[Reminder 2] Don\'t forgot to complete your order',
            'html'    => $html,
        ]);

        // Create campaign
        $this->loadCampaign($segment, [$email1, $email2]);

        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    private function loadCustomObject(): CustomObject
    {
        // Create Custom Object
        $product = new CustomObject();
        $product->setAlias('product');
        $product->setNameSingular('Product');
        $product->setNamePlural('Products');
        $product->setDescription('Details about Products');
        $product->setCreatedByUser($this->adminUser);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        // Create Custom Object fields
        $this->createCustomField($product, [
            'alias'   => 'type',
            'label'   => 'Type',
            'type'    => 'select',
            'options' => [
                'list' => [
                    [
                        'label' => self::ABANDONED_LABEL,
                        'value' => self::ABANDONED_VALUE,
                    ],
                    [
                        'label' => self::WISHLIST_LABEL,
                        'value' => self::WISHLIST_VALUE,
                    ],
                    [
                        'label' => self::REVIEW_LABEL,
                        'value' => self::REVIEW_VALUE,
                    ],
                ],
            ],
        ]);

        $this->createCustomField($product, [
            'alias' => 'description',
            'label' => 'Description',
            'type'  => 'textarea',
        ]);

        $this->createCustomField($product, [
            'alias' => 'link',
            'label' => 'Link',
            'type'  => 'url',
        ]);

        $this->createCustomField($product, [
            'alias' => 'thumbnail',
            'label' => 'Thumbnail Image',
            'type'  => 'url',
        ]);

        $this->createCustomField($product, [
            'alias'            => 'sku',
            'label'            => 'SKU',
            'type'             => 'text',
        ]);

        $this->createCustomField($product, [
            'alias' => 'quantity',
            'label' => 'Quantity',
            'type'  => 'text',
        ]);

        $this->createCustomField($product, [
            'alias' => 'price',
            'label' => 'Price',
            'type'  => 'text',
        ]);

        $this->createCustomField($product, [
            'alias' => 'checkout_link',
            'label' => 'Checkout Link',
            'type'  => 'url',
        ]);

        return $product;
    }

    /**
     * @param array<string, mixed> $properties
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
        $cf->setCreatedBy($this->adminUser);
        $cf->setModifiedBy($this->adminUser);
        $cf->setDateAdded(new \DateTime());
        $cf->setDateModified(new \DateTime());

        if (!empty($properties['options']['list'])) {
            foreach ($properties['options']['list'] as $key => $option) {
                $this->createCustomFieldOption($cf, $option['label'], $option['value'], $key);
            }
        }

        $this->entityManager->persist($cf);

        $this->entityManager->flush();
    }

    private function createCustomFieldOption(CustomField $customField, string $label, string $value, int $order): void
    {
        $customFieldOption = new CustomFieldOption();
        $customFieldOption->setCustomField($customField);
        $customFieldOption->setLabel($label);
        $customFieldOption->setValue($value);
        $customFieldOption->setOrder($order);

        $this->entityManager->persist($customFieldOption);
    }

    private function loadSegments(CustomObject $product): LeadList
    {
        $segmentDetails = [
            'name'    => 'Abandoned Card Contacts',
            'alias'   => 'abandoned_card_contacts',
            'filters' => [
                [
                    'glue'       => 'and',
                    'field'      => 'cmo_'.$product->getId(), // Name field of CO.
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

        return $this->createSegment($segmentDetails);
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
        $segment->setModifiedBy($this->adminUser);
        $segment->setDateAdded(new \DateTime());
        $segment->setDateModified(new \DateTime());

        if (!empty($segmentDetails['filters'])) {
            $segment->setFilters($segmentDetails['filters']);
        }

        $this->entityManager->persist($segment);
        $this->entityManager->flush();

        return $segment;
    }

    /**
     * @param array<string, mixed> $details
     */
    private function loadEmail(array $details): Email
    {
        $email = new Email();
        $email->setName($details['name']);
        $email->setSubject($details['subject']);
        $email->setEmailType('template');

        $email->setCustomHtml($details['html']);
        $email->setIsPublished(true);
        $email->setCreatedBy($this->adminUser);
        $email->setModifiedBy($this->adminUser);
        $email->setDateAdded(new \DateTime());
        $email->setDateModified(new \DateTime());

        $this->entityManager->persist($email);

        return $email;
    }

    /**
     * @param Email[] $emails
     */
    private function loadCampaign(LeadList $segment, array $emails): void
    {
        $campaign = new Campaign();
        $campaign->setName('Abandoned Card Reminder');
        $campaign->setCreatedBy($this->adminUser);
        $campaign->setModifiedBy($this->adminUser);
        $campaign->setIsPublished(true);
        $campaign->setAllowRestart(true);
        $campaign->setDateAdded(new \DateTime());
        $campaign->setDateModified(new \DateTime());
        $campaign->addList($segment);

        $this->entityManager->persist($campaign);
        $this->entityManager->flush();

        list($firstEmail, $secondEmail) = $emails;

        // First reminder email
        $details = [
            'name'       => 'First Reminder',
            'type'       => 'email.send',
            'eventType'  => Event::TYPE_ACTION,
            'order'      => 1,
            'properties' => [
                'email'      => $firstEmail->getId(),
                'email_type' => 'transactional',
                'priority'   => '2',
                'attempts'   => '3',
                'properties' => [
                    'email'      => $firstEmail->getId(),
                    'email_type' => 'transactional',
                    'priority'   => '2',
                    'attempts'   => '3',
                ],
            ],
        ];
        $event1 = $this->createEvent($campaign, $details);
        $event1->setTriggerMode('immediate');

        $this->entityManager->persist($event1);
        $this->entityManager->flush();

        $details = [
            'name'       => 'Read the First Reminder',
            'type'       => 'email.open',
            'eventType'  => Event::TYPE_DECISION,
            'order'      => 2,
            'properties' => [],
        ];
        $event2 = $this->createEvent($campaign, $details);

        // Second reminder email
        $details = [
            'name'       => 'Send second reminder',
            'type'       => 'email.send',
            'eventType'  => Event::TYPE_ACTION,
            'order'      => 3,
            'properties' => [
                'email'      => $secondEmail->getId(),
                'email_type' => 'transactional',
                'priority'   => '2',
                'attempts'   => '3',
                'properties' => [
                    'email'      => $secondEmail->getId(),
                    'email_type' => 'transactional',
                    'priority'   => '2',
                    'attempts'   => '3',
                ],
            ],
        ];
        $event3 = $this->createEvent($campaign, $details);
        $event3->setTriggerMode('interval');
        $event3->setTriggerInterval(7);
        $event3->setTriggerIntervalUnit('d');
        $event3->setDecisionPath('no');
        $event3->setParent($event2);

        $this->entityManager->persist($event3);
        $this->entityManager->flush();

        $campaign->setCanvasSettings([
            'nodes' => [
                [
                    'id'        => $event1->getId(),
                    'positionX' => '750',
                    'positionY' => '150',
                ],
                [
                    'id'        => $event2->getId(),
                    'positionX' => '750',
                    'positionY' => '250',
                ],
                [
                    'id'        => $event3->getId(),
                    'positionX' => '900',
                    'positionY' => '350',
                ],
                [
                    'id'        => 'lists',
                    'positionX' => '750',
                    'positionY' => '50',
                ],
            ],
            'connections' => [
                [
                    'sourceId' => 'lists',
                    'targetId' => $event1->getId(),
                    'anchors'  => [
                        'source' => 'leadsource',
                        'target' => 'top',
                    ],
                ],
                [
                    'sourceId' => $event1->getId(),
                    'targetId' => $event2->getId(),
                    'anchors'  => [
                        'source' => 'bottom',
                        'target' => 'top',
                    ],
                ],
                [
                    'sourceId' => $event2->getId(),
                    'targetId' => $event3->getId(),
                    'anchors'  => [
                        'source' => 'no',
                        'target' => 'top',
                    ],
                ],
            ],
        ]);

        $this->entityManager->persist($campaign);

        $this->entityManager->flush();
        $this->entityManager->clear();
    }

    /**
     * @param array<string, mixed> $details
     */
    private function createEvent(Campaign $campaign, array $details): Event
    {
        $event = new Event();
        $event->setCampaign($campaign);
        $event->setName($details['name']);
        $event->setType($details['type']);
        $event->setEventType($details['eventType']);
        $event->setOrder($details['order']);
        $event->setProperties($details['properties']);

        $this->entityManager->persist($event);
        $this->entityManager->flush();

        return $event;
    }
}
