<?php

namespace Atelier;

use Monolog\Handler\ZendMonitorHandler;

class Url
{
    /**
     * @var mixed|string
     */
    private string $url;
    private string $path;

    public function __construct(string $url = '')
    {
        $this->url = $url ?: ($_SERVER['REQUEST_URI'] ?? '/');
        $this->path = $this->initPath();
    }

    public function __toString(): string
    {
        return $this->url;
    }

    public function getSubDomains(): array
    {
        return explode('.', explode('//', $this->getDomain())[1]);
    }

    public function getDomain(): string
    {
        $parsed = parse_url($this->url);
        if (isset($parsed['scheme']) && isset($parsed['host'])) {
            return $parsed['scheme'] . '://' . $parsed['host'];
        }

        return '';
    }

    public function getFull(): string
    {
        return $this->url;
    }

    public function getPath(): string
    {
        return $this->path;
    }

    public function generate(array $replaces): self
    {
        $parameters = $this->getQueryParameters();
        foreach ($replaces as $key => $value) {
            if (is_null($value) && isset($parameters[$key])) {
                unset($parameters[$key]);
            } else {
                $parameters[$key] = $value;
            }
        }

        $url = $this->getPath() . ($parameters ? '?' . http_build_query($parameters) : '');

        return new self($url);
    }

    public function getQueryParameters(): array
    {
        $path = parse_url($this->url);
        parse_str($path['query'] ?? '', $parameters);
        foreach ($parameters as &$value) {
            $value = Filter::get($value);
        }

        return $parameters;
    }

    public function generateParentUrl(): self
    {
        $parts = $this->getParts();
        array_pop($parts);

        return new self(
            '/'
            . implode('/', $parts)
            . ($this->getQueryParameters()
                ? '?' . http_build_query($this->getQueryParameters())
                : ''
            )
        );
    }

    public function isAdPage(): bool
    {
        $last = $this->getLastPart($this->path);

        return mb_substr($last, 0, 2) == 'ad'
            && is_numeric($this->getAdId())
            && $this->getAdId();
    }

    public function getPageNumber(): int
    {
        if ($this->hasUrlPageNumber()) {
            $withoutQueryUrl = parse_url($this->url)['path'] ?? '/';

            return $this->getLastPart($withoutQueryUrl);
        }

        return 1;
    }

    public function getParts(): array
    {
        return array_values(array_filter(explode('/', $this->path)));
    }

    public function isStartsAt(array $paths): bool
    {
        foreach ($paths as $path) {
            if (mb_strpos($this->getPath(), $path) === 0) {
                return true;
            }
        }

        return false;
    }

    private function hasUrlPageNumber(): bool
    {
        $withoutQueryUrl = parse_url($this->url)['path'] ?? '/';
        $parts = self::getUrlParts($withoutQueryUrl);

        return isset($parts[count($parts) - 1]) && is_numeric($parts[count($parts) - 1]);
    }

    private function getLastPart(string $path): string
    {
        $parts = $this->getUrlParts($path);

        return $parts[count($parts) - 1] ?? '';
    }

    private function getUrlParts(string $url): array
    {
        return array_values(array_filter(explode('/', $url)));
    }

    private function initPath(): string
    {
        $path = parse_url($this->url)['path'] ?? '/';
        if (mb_substr($path, -1) == '/' && $path != '/') {
            $path = mb_substr($path, 0, -1);
        }

        if ($this->hasUrlPageNumber()) {
            $path = $this->withoutPageNumber($path);
        }

        return $path;
    }

    private function withoutPageNumber(string $path): string
    {
        if ($this->hasUrlPageNumber()) {
            $parts = $this->getUrlParts($path);
            array_pop($parts);

            return '/' . implode('/', array_filter($parts));
        }

        return $path;
    }
}