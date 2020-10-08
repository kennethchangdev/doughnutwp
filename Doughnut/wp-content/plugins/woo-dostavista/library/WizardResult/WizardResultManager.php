<?php

namespace WooDostavista\WizardResult;

use WooDostavista\DostavistaAuth\DostavistaApiAuthTokenVerifier;
use WooDostavista\ModuleConfig\ModuleConfig;
use WooDostavista\ModuleMetric\ModuleMetricManager;
use WooDostavista\ModuleSettings\ModuleSettings;

class WizardResultManager
{
    const WIZARD_MAX_STEP_NUMBER = 6;

    public static function getIsWizardFinished(): bool
    {
        $settings = static::getSettings();

        $isAuthTokenValid = DostavistaApiAuthTokenVerifier::isCmsModuleApiTokenValid();

        // Визард считается пройденным, если все шаги закончены и токен существует
        return $isAuthTokenValid && $settings->getWizardLastFinishedStep() === static::WIZARD_MAX_STEP_NUMBER;
    }

    public static function setLastFinishedStep(int $stepNum)
    {
        global $wpdb;
        $settings = static::getSettings();

        if ($settings->getWizardLastFinishedStep() < $stepNum && $stepNum <= static::WIZARD_MAX_STEP_NUMBER) {
            $settings->setWizardLastFinishedStep($stepNum);
        }

        // Отправим событие
        $moduleConfig = new ModuleConfig();
        $moduleMetricManager = new ModuleMetricManager(
            $wpdb,
            $moduleConfig->getDvCmsModuleApiProdUrl(),
            $settings->getApiUrl(),
            $settings->getAuthToken()
        );

        $moduleMetricManager->wizardStepCompleted($stepNum);
    }

    private static function getSettings(): ModuleSettings
    {
        global $wpdb;
        return (new ModuleSettings($wpdb));
    }
}
