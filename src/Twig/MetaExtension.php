<?php
namespace HouseOfAgile\NakaCMSBundle\Twig;

use Twig\TwigFilter;
use Twig\Extension\AbstractExtension;
use function Symfony\Component\String\u;
use Symfony\Contracts\Translation\TranslatorInterface;
use HouseOfAgile\NakaCMSBundle\Seo\MetaTextProviderInterface;

final class MetaExtension extends AbstractExtension
{
    public function __construct(private readonly TranslatorInterface $translator) {}

	public function getFilters(): array
    {
        $safe = ['is_safe' => ['html']];
        return [
            new TwigFilter('meta_description', [$this, 'metaDescription'], $safe),
            new TwigFilter('og_description', [$this, 'ogDescription'], $safe),
        ];
    }

    /** 50–160 chars */
    public function metaDescription(object $subject): string
    {
        return $this->build($subject, 155);
    }

    /** up to ~300 chars */
    public function ogDescription(object $subject): string
    {
        return $this->build($subject, 300);
    }

    private function build(object $subject, int $limit): string
    {
        $raw = $this->pickFirstNonEmpty($subject);

        $plain = $this->normalizePlainText($raw ?? '');

        $short = (string) u($plain)->truncate($limit, '…', false);

        return $short !== ''
            ? $short
            : $this->translator->trans('common.defaultMetaDescription');
    }

    private function normalizePlainText(string $value): string
    {
        $plain = strip_tags($value);
        for ($i = 0; $i < 2; $i++) {
            $decoded = html_entity_decode($plain, ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($decoded === $plain) {
                break;
            }
            $plain = $decoded;
        }

        return trim(preg_replace('/\s+/u', ' ', $plain));
    }

    /** Finds the first usable string from provider or common getters */
    private function pickFirstNonEmpty(object $subject): ?string
    {
        // Preferred: entity honours the contract
        if ($subject instanceof MetaTextProviderInterface) {
            foreach ($subject->getMetaCandidates() as $candidate) {
                if ($candidate && trim(strip_tags($candidate)) !== '') {
                    return $candidate;
                }
            }
        }

        // Fallback: look for conventional getters
        foreach ([
            'getMetaDescription',
            'getSummary',
            'getSensitiveContent',
            'getContent',
            'getBody',
            'getText',
        ] as $getter) {
            if (method_exists($subject, $getter) && ($val = $subject->$getter())) {
                if (trim(strip_tags($val)) !== '') {
                    return $val;
                }
            }
        }
        return null;
    }
}
