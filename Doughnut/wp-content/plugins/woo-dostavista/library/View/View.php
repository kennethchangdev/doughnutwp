<?php

namespace WooDostavista\View;

class View
{
    /** @var string */
    private $viewDestination;

    /** @var array */
    private $params;

    public function __construct(string $viewDestination, array $params = [])
    {
        $this->viewDestination = $viewDestination;
        $this->params          = $params;
    }

    public function getRenderedHtml(): string
    {
        extract($this->params);

        ob_start();
        require ($this->viewDestination);
        $html = ob_get_contents();
        ob_end_clean();

        return $html;
    }
}
