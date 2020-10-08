<?php

namespace WooDostavista\ModuleMetric;

use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiClient;
use WooDostavista\DvCmsModuleApiClient\DvCmsModuleApiHttpException;
use WooDostavista\DvCmsModuleApiClient\Request\AddEventRequestModel;
use wpdb;

class ModuleMetricManager
{
    /** @var wpdb */
    private $wpdb;

    /** @var string */
    private $cmsApiUrl;

    /** @var string */
    private $cmsModuleApiUrl;

    /** @var string|null */
    private $authToken;

    public function __construct(wpdb $wpdb, string $cmsApiUrl, string $cmsModuleApiUrl, string $authToken = null)
    {
        $this->wpdb            = $wpdb;
        $this->cmsApiUrl       = $cmsApiUrl;
        $this->cmsModuleApiUrl = $cmsModuleApiUrl;
        $this->authToken       = $authToken;

        $this->createTablesIfNotExists();
    }

    public function createTablesIfNotExists()
    {
        $this->wpdb->query(
            "
                CREATE TABLE IF NOT EXISTS `woo_dostavista_metrics` (
                    `id` INT(11) NOT NULL AUTO_INCREMENT,
                    `key` VARCHAR(255) DEFAULT '',
                    `value` VARCHAR(1024) DEFAULT '',
                    PRIMARY KEY (`id`),
                    UNIQUE KEY (`key`)
                ) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_general_ci;
            "
        );
    }

    public function setData(string $key, string $value)
    {
        if ($this->issetKey($key)) {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "UPDATE `woo_dostavista_metrics` SET `value` = %s WHERE `key` = %s",
                    trim($value), $key
                )
            );
        } else {
            $this->wpdb->query(
                $this->wpdb->prepare(
                    "INSERT INTO `woo_dostavista_metrics` (`key`, `value`) VALUES (%s, %s)",
                    $key, trim($value)
                )
            );
        }
    }

    /**
     * @param string $key
     * @return mixed|null
     */
    public function getData(string $key)
    {
        $rows = $this->wpdb->get_results(
            $this->wpdb->prepare("SELECT `value` FROM `woo_dostavista_metrics` WHERE `key` = %s", $key)
        );

        return $rows[0]->value ?? null;
    }

    private function issetKey(string $key): bool
    {
        $queryResult = $this->wpdb->query(
            $this->wpdb->prepare("SELECT `value` FROM `woo_dostavista_metrics` WHERE `key` = %s", $key)
        );
        return (bool) $queryResult;
    }

    private function getApiClient(bool $withToken = true)
    {
        return new DvCmsModuleApiClient(
            $this->cmsApiUrl,
            $withToken ? $this->authToken : null
        );
    }

    public function install(string $datetime = null)
    {
        $this->setData('install_datetime', date('c', strtotime($datetime ?? 'now')));
        $this->sendModuleMetrics();
    }

    public function uninstall(string $datetime = null)
    {
        $this->setData('uninstall_datetime', date('c', strtotime($datetime ?? 'now')));
        $this->sendModuleMetrics();
    }

    public function tokenCreate(string $datetime = null)
    {
        $this->setData('token_create_datetime', date('c', strtotime($datetime ?? 'now')));
        $this->sendModuleMetrics();
    }

    public function tokenInstall(string $datetime = null)
    {
        $this->setData('token_install_datetime', date('c', strtotime($datetime ?? 'now')));
        $this->sendModuleMetrics();
    }

    public function deliveryInstall(string $datetime = null)
    {
        $this->setData('delivery_install_datetime', date('c', strtotime($datetime ?? 'now')));
        $this->sendModuleMetrics();
    }

    public function deliveryUninstall(string $datetime = null)
    {
        $this->setData('delivery_uninstall_datetime', date('c', strtotime($datetime ?? 'now')));
        $this->sendModuleMetrics();
    }

    public function callbackKeyInstall(string $datetime = null)
    {
        $this->setData('callback_key_install_datetime', date('c', strtotime($datetime ?? 'now')));
        $this->sendModuleMetrics();
    }

    private function sendModuleMetrics()
    {
        $domain = $_SERVER['HTTP_HOST'] ?? $_SERVER['SERVER_NAME'];
        if (isset($_SERVER['REQUEST_SCHEME']) && $_SERVER['REQUEST_SCHEME'] === 'https') {
            $url = 'https://' . $domain;
        } else {
            $url = 'http://' . $domain;
        }

        $notAuthMetricMap = [
            AddEventRequestModel::EVENT_TYPE_INSTALL   => ['install_datetime', 'install_notified_datetime'],
            AddEventRequestModel::EVENT_TYPE_UNINSTALL => ['uninstall_datetime', 'uninstall_notified_datetime'],
        ];
        $notAuthApiClient = static::getApiClient();
        foreach ($notAuthMetricMap as $eventType => $fields) {
            $eventDatetimeField         = $fields[0];
            $eventNotifiedDatetimeField = $fields[1];

            if ($this->getData($eventDatetimeField) > $this->getData($eventNotifiedDatetimeField)) {
                try {
                    $notAuthApiClient->addEvent(
                        new AddEventRequestModel(
                            $eventType, $url, $this->getData($eventDatetimeField)
                        )
                    );
                    $this->setData($eventNotifiedDatetimeField, date('c'));
                } catch (DvCmsModuleApiHttpException $e) {
                    // Пока проблемы с API просто гасим. Метрики дойдут при следующих попытках
                }
            }
        }

        $isTestApiToken = strpos($this->cmsModuleApiUrl, 'robotapitest.') !== false;
        if (!$isTestApiToken && $this->cmsModuleApiUrl && $this->authToken) {
            $authMetricMap = [
                AddEventRequestModel::EVENT_TYPE_TOKEN_CREATE         => ['token_create_datetime', 'token_create_notified_datetime'],
                AddEventRequestModel::EVENT_TYPE_TOKEN_INSTALL        => ['token_install_datetime', 'token_install_notified_datetime'],
                AddEventRequestModel::EVENT_TYPE_DELIVERY_INSTALL     => ['delivery_install_datetime', 'delivery_install_notified_datetime'],
                AddEventRequestModel::EVENT_TYPE_DELIVERY_UNINSTALL   => ['delivery_uninstall_datetime', 'delivery_uninstall_notified_datetime'],
                AddEventRequestModel::EVENT_TYPE_CALLBACK_KEY_INSTALL => ['callback_key_install_datetime', 'callback_key_install_notified_datetime'],

                AddEventRequestModel::EVENT_TYPE_WIZARD_STEP_1_COMPLETED => ['wizard_step_1_completed_datetime', 'wizard_step_1_completed_notified_datetime'],
                AddEventRequestModel::EVENT_TYPE_WIZARD_STEP_2_COMPLETED => ['wizard_step_2_completed_datetime', 'wizard_step_2_completed_notified_datetime'],
                AddEventRequestModel::EVENT_TYPE_WIZARD_STEP_3_COMPLETED => ['wizard_step_3_completed_datetime', 'wizard_step_3_completed_notified_datetime'],
                AddEventRequestModel::EVENT_TYPE_WIZARD_STEP_4_COMPLETED => ['wizard_step_4_completed_datetime', 'wizard_step_4_completed_notified_datetime'],
                AddEventRequestModel::EVENT_TYPE_WIZARD_STEP_5_COMPLETED => ['wizard_step_5_completed_datetime', 'wizard_step_5_completed_notified_datetime'],
                AddEventRequestModel::EVENT_TYPE_WIZARD_STEP_6_COMPLETED => ['wizard_step_6_completed_datetime', 'wizard_step_6_completed_notified_datetime'],
            ];

            $authApiClient = static::getApiClient(true);
            foreach ($authMetricMap as $eventType => $fields) {
                $eventDatetimeField         = $fields[0];
                $eventNotifiedDatetimeField = $fields[1];

                if ($this->getData($eventDatetimeField) > $this->getData($eventNotifiedDatetimeField)) {
                    try {
                        $authApiClient->addEvent(
                            new AddEventRequestModel(
                                $eventType, $url, $this->getData($eventDatetimeField)
                            )
                        );
                        $this->setData($eventNotifiedDatetimeField, date('c'));
                    } catch (DvCmsModuleApiHttpException $e) {
                        // Пока проблемы с API просто гасим. Метрики дойдут при следующих попытках
                    }
                }
            }
        }
    }

    public function wizardStepCompleted(int $stepNumber, string $datetime = null)
    {
        $fieldName = '';
        switch ($stepNumber) {
            case 1:
                $fieldName = 'wizard_step_1_completed_datetime';
                break;
            case 2:
                $fieldName = 'wizard_step_2_completed_datetime';
                break;
            case 3:
                $fieldName = 'wizard_step_3_completed_datetime';
                break;
            case 4:
                $fieldName = 'wizard_step_4_completed_datetime';
                break;
            case 5:
                $fieldName = 'wizard_step_5_completed_datetime';
                break;
            case 6:
                $fieldName = 'wizard_step_6_completed_datetime';
                break;
        }

        if (!$fieldName) {
            return;
        }

        $this->setData($fieldName, date('c', strtotime($datetime ?? 'now')));

        $this->sendModuleMetrics();
    }
}
