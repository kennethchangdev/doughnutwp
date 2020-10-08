<?php

namespace WooDostavista\GeneralCache;

class CacheItem
{
    /** @var string */
    private $key;

    /** @var string */
    private $value;

    /** @var int */
    private $expirationInSeconds;

    /** @var string|null */
    private $group;

    public function __construct(string $key, string $value, string $group = null, int $expirationInSeconds = 0)
    {
        $this->key   = $key;
        $this->group = $group;
        $this->set($value);
        $this->setExpirationInSeconds($expirationInSeconds);
    }

    /**
     * @param string $value
     * @return CacheItem
     */
    public function set($value)
    {
        $this->value = $value;
        return $this;
    }

    public function get(): string
    {
        return $this->value;
    }

    public function getKey(): string
    {
        return $this->key;
    }

    public function setExpirationInSeconds(int $seconds)
    {
        $this->expirationInSeconds = $seconds;
    }

    public function getExpirationInSeconds(): int
    {
        return $this->expirationInSeconds;
    }

    /**
     * @return string|null
     */
    public function getGroup()
    {
        return $this->group;
    }
}