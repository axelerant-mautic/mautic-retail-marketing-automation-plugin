<?php

declare(strict_types=1);

namespace MauticPlugin\RetailMarketingBundle\Helper;

use Twig\Environment;

class TokenFormatter
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * @param array<int, mixed> $customItems
     */
    public function format(array $customItems): string
    {
        return $this->twig->render('RetailMarketingBundle:AbandonedCart:list.html.twig', [
            'items' => $customItems,
        ]);
    }
}
