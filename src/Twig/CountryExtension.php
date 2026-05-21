<?php
// src/Twig/CountryExtension.php
namespace App\Twig;

use Symfony\Component\Intl\Countries;
use Twig\Extension\AbstractExtension;
use Twig\TwigFilter;

class CountryExtension extends AbstractExtension
{
    public function getFilters(): array
    {
        return [
            new TwigFilter('countryFlagEmoji', [$this, 'getCountryFlagEmoji']),
            new TwigFilter('countryName', [$this, 'getCountryName']),
        ];
    }

    /**
     * Retourne le drapeau emoji (ex: FR → 🇫🇷)
     */
    public function getCountryFlagEmoji(?string $countryCode): string
    {
        if (!$countryCode || strlen($countryCode) !== 2) {
            return '🌍';
        }

        return mb_convert_encoding('&#' . (127397 + ord(strtoupper($countryCode[0]))) . ';', 'UTF-8', 'HTML-ENTITIES') .
            mb_convert_encoding('&#' . (127397 + ord(strtoupper($countryCode[1]))) . ';', 'UTF-8', 'HTML-ENTITIES');
    }

    /**
     * Retourne le nom complet du pays (ex: FR → France)
     */
    public function getCountryName(?string $countryCode, string $locale = 'fr'): ?string
    {
        if (!$countryCode || strlen($countryCode) !== 2) {
            return null;
        }

        try {
            return Countries::getName($countryCode, $locale);
        } catch (\Exception $e) {
            return strtoupper($countryCode);
        }
    }
}
