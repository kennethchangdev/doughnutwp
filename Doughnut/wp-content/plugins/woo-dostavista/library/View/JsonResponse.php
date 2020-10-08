<?php

namespace WooDostavista\View;

class JsonResponse
{
    /** @var array */
    private $params;

    /** @var int */
    private $httpCode;

    /** @var array */
    private $headers;

    public function __construct(array $params, int $httpCode = 200, array $headers = [])
    {
        $this->params   = $params;
        $this->httpCode = $httpCode;
        $this->headers  = $headers;

        $this->headers['Content-Type'] = 'application/json';
    }

    public function render()
    {
        foreach ($this->headers as $name => $value) {
            header(sprintf('%s: %s', $name, $value), true);
        }

        http_response_code($this->httpCode);
        echo json_encode($this->params, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
    }
}

