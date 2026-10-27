<?php

namespace App\Support;

use DOMDocument;
use DOMElement;
use DOMNode;

/**
 * Small allow-list sanitizer for admin-authored rich text. No package
 * required; strips scripts, event handlers, styles and unknown tags while
 * keeping the structural markup the editor can produce.
 */
class HtmlSanitizer
{
    private const ALLOWED = [
        'p', 'br', 'strong', 'b', 'em', 'i', 'u', 'h2', 'h3', 'h4',
        'ul', 'ol', 'li', 'blockquote', 'a', 'hr',
    ];

    public static function clean(?string $html): ?string
    {
        if ($html === null || trim($html) === '') {
            return $html;
        }

        $document = new DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML(
            '<?xml encoding="UTF-8"><div id="sanitizer-root">'.$html.'</div>',
            LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD | LIBXML_NONET
        );
        libxml_clear_errors();
        libxml_use_internal_errors($previous);

        $root = $document->getElementById('sanitizer-root')
            ?: $document->getElementsByTagName('div')->item(0);

        if (! $root) {
            return '';
        }

        self::walk($root);

        $output = '';
        foreach (iterator_to_array($root->childNodes) as $child) {
            $output .= $document->saveHTML($child);
        }

        return trim($output);
    }

    private static function walk(DOMNode $node): void
    {
        foreach (iterator_to_array($node->childNodes) as $child) {
            if ($child instanceof DOMElement) {
                $tag = strtolower($child->tagName);

                if (! in_array($tag, self::ALLOWED, true)) {
                    // Sanitize the children BEFORE lifting them out. Once they are
                    // moved into the parent the loop snapshot no longer contains
                    // them, so anything nested inside a removed tag (<span>, <div>,
                    // <font>... typical of Word/Docs pastes) kept its onclick/onerror
                    // attributes and javascript: links.
                    self::walk($child);
                    self::unwrap($child);

                    continue;
                }

                foreach (iterator_to_array($child->attributes) as $attribute) {
                    $name = strtolower($attribute->name);

                    if ($tag === 'a' && $name === 'href' && self::safeUrl($attribute->value)) {
                        $child->setAttribute('rel', 'noopener noreferrer');
                        $child->setAttribute('target', '_blank');

                        continue;
                    }

                    $child->removeAttribute($attribute->name);
                }

                self::walk($child);
            }
        }
    }

    private static function unwrap(DOMElement $element): void
    {
        $parent = $element->parentNode;

        if (! $parent) {
            return;
        }

        while ($element->firstChild) {
            $parent->insertBefore($element->firstChild, $element);
        }

        $parent->removeChild($element);
    }

    private static function safeUrl(string $url): bool
    {
        $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));

        return in_array($scheme, ['http', 'https', 'mailto'], true);
    }
}
