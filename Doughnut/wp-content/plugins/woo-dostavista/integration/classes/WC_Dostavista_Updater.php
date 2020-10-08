<?php

use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiHttpException;
use WooDostavista\ModuleSettings\ModuleSettings;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;
use WooDostavista\WizardResult\WizardResultManager;

class WC_Dostavista_Updater
{
    /** @var ModuleSettings */
    private $settings;

    public function __construct()
    {
        global $wpdb;
        $this->settings = new ModuleSettings($wpdb);
    }

    public function init()
    {
        // С версии 1.1 поменяли API
        $this->changeBusinessApiToCmsModuleApi();

        // C версии 1.2 изменили визард. Теперь пройденные шаги сохраняются
        $this->updateWizardLastFinishedStep();
    }

    public function changeBusinessApiToCmsModuleApi()
    {
        $data = $this->settings->getData();
        $isUpdated = false;

        $dvBusinessApiUrl           = $data['dostavista_business_api_url'] ?? '';
        $dvBusinessApiAuthToken     = $data['dostavista_business_api_auth_token'] ?? '';
        $dvBusinessApiCallbackToken = $data['dostavista_business_api_callback_secret'] ?? '';

        $isApiTest = strpos($dvBusinessApiUrl, 'robotapitest') !== false;
        if (!$this->settings->issetKey('dostavista_is_api_test_server')) {
            $this->settings->setData(
                'dostavista_is_api_test_server',
                (int) $isApiTest
            );
            $isUpdated = true;
        }

        if (!$this->settings->issetKey('dostavista_cms_module_api_test_auth_token')) {
            $this->settings->setCmsModuleApiTestAuthToken(
                $isApiTest ? $dvBusinessApiAuthToken : ''
            );
            $isUpdated = true;
        }

        if (!$this->settings->issetKey('dostavista_cms_module_api_prod_auth_token')) {
            $this->settings->setCmsModuleApiProdAuthToken(
                !$isApiTest ? $dvBusinessApiAuthToken : ''
            );
            $isUpdated = true;
        }

        if (!$this->settings->issetKey('dostavista_cms_module_api_callback_secret')) {
            $this->settings->setData(
                'dostavista_cms_module_api_callback_secret',
                $dvBusinessApiCallbackToken
            );
            $isUpdated = true;
        }

        if ($isUpdated && $this->settings->getApiUrl() && $this->settings->getAuthToken()) {
            $dvCmsModuleApiClient = new DvCmsModuleApiClient($this->settings->getApiUrl(), $this->settings->getAuthToken());
            try {
                $apiEditSettingsResponse = $dvCmsModuleApiClient->editApiSettings(admin_url('admin-post.php?action=woo_dostavista/api_callback_handler'));
                $this->settings->setData('dostavista_cms_module_api_callback_secret', $apiEditSettingsResponse->getCallbackSecretKey());
            } catch (DvCmsModuleApiHttpException $exception) {

            }
        }
    }

    public function updateWizardLastFinishedStep()
    {
        $data = $this->settings->getData();

        // Если у пользовтеля есть токен, то считаем, что это старый пользователь, и устанавливаем ему визард пройденным
        if (!isset($data['wizard_last_finished_step']) && $this->settings->getAuthToken()) {
            $this->settings->setData(
                'wizard_last_finished_step',
                WizardResultManager::WIZARD_MAX_STEP_NUMBER
            );
        } elseif (!isset($data['wizard_last_finished_step']) && !$this->settings->getAuthToken()) {
            $this->settings->setData(
                'wizard_last_finished_step',
                0
            );
        }
    }
}
