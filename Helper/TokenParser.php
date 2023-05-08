<?php

declare(strict_types=1);

namespace MauticPlugin\RetailMarketingBundle\Helper;

use Doctrine\Common\Collections\ArrayCollection;
use MauticPlugin\CustomObjectsBundle\DTO\Token;

class TokenParser
{
    public const TOKEN = '{custom-object-list=(.*?)}';

    /**
     * @return ArrayCollection<int, Token>
     */
    public function findTokens(string $content): ArrayCollection
    {
        $tokens = new ArrayCollection();

        preg_match_all('/'.self::TOKEN.'/', $content, $matches);

        if (empty($matches[1])) {
            return $tokens;
        }

        $token = new Token($matches[0][0]);
        $token->setLimit(10);
        $token->setCustomObjectAlias($matches[1][0]);
        $tokens->set($token->getToken(), $token);

        return $tokens;
    }

    public function buildTokenWithDefaultOptions(string $customObjectAlias): string
    {
        return "{custom-object-list={$customObjectAlias}}";
    }

    public function buildTokenLabel(string $customObjectName): string
    {
        return "{$customObjectName}: List View";
    }
}
