<?php

namespace WooDostavista\ModuleConfig;

class ModuleConfig
{
    /** @var array */
    private $data;

    public function __construct()
    {
        $configData = include (__DIR__ . '/module_config.php');
        $this->data = is_array($configData) ? $configData : [];
    }

    public function getDvCmsModuleApiProdUrl(): string
    {
        return $this->data['dv_cms_module_api_prod_url'];
    }

    public function getDvCmsModuleApiTestUrl(): string
    {
        return $this->data['dv_cms_module_api_test_url'];
    }
}
