<?php

namespace WooDostavista\DvCmsModuleApiClient;

class DvCmsModuleApiRequest
{
    const HTTP_METHOD_POST = 'POST';
    const HTTP_METHOD_GET  = 'GET';

    /** @var array */
    private $data = [];

    /** @var string */
    private $httpMethod;

    /** @var string */
    private $apiMethod;

    /** @var array header => value */
    private $headers;

    /** @var int */
    private $attemptsCount;

    public function __construct(array $data, string $httpMethod, string $apiMethod, array $headers = [])
    {
        $this->data       = $data;
        $this->httpMethod = $httpMethod;
        $this->apiMethod  = $apiMethod;
        $this->headers    = $headers;

        if ($this->httpMethod === static::HTTP_METHOD_GET) {
            // GET запросы пытаемся повторять по умолчанию 3 раза
            $this->attemptsCount = 3;
        } else {
            // POST, DELETE, PUT запросы нельзя повторять по умолчаниюZ
            $this->attemptsCount = 1;
        }
    }

    public function getData(): array
    {
        return $this->data;
    }

    public function getHttpMethod(): string
    {
        return $this->httpMethod;
    }

    public function getApiMethod(): string
    {
        return $this->apiMethod;
    }

    public function getHeaders(): array
    {
        return $this->headers;
    }

    public function getRequestUrl(): string
    {
        return $this->getHttpMethod() === static::HTTP_METHOD_POST
            ? $this->getApiMethod()
            : $this->getApiMethod() . '?' . http_build_query($this->getData());
    }

    public function setAttemptsCount(int $count): DvCmsModuleApiRequest
    {
        $this->attemptsCount = $count;
        return $this;
    }

    public function getAttemptsCount(): int
    {
        return $this->attemptsCount;
    }
}
