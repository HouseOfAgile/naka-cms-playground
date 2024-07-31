<?php

namespace HouseOfAgile\NakaCMSBundle\Config;

use Symfony\Contracts\Translation\TranslatableInterface;
use Symfony\Contracts\Translation\TranslatorInterface;

enum AdNetworkType: string implements TranslatableInterface
{
    case GOOGLE_ADSENSE = 'google_ad_sense';

    public function trans(TranslatorInterface $translator, string $locale = null): string
    {
        // Translate enum using custom labels
        return match ($this) {
            self::GOOGLE_ADSENSE  => $translator->trans('adNetworkType.googleAdSense', locale: $locale),
        };
    }
}
