<?php

namespace WooDostavista\DvCmsModuleApiClient\Request;

class AddEventRequestModel
{
    const EVENT_TYPE_INSTALL              = 'install';
    const EVENT_TYPE_UNINSTALL            = 'uninstall';
    const EVENT_TYPE_TOKEN_CREATE         = 'token_create';
    const EVENT_TYPE_TOKEN_INSTALL        = 'token_install';
    const EVENT_TYPE_DELIVERY_INSTALL     = 'delivery_install';
    const EVENT_TYPE_DELIVERY_UNINSTALL   = 'delivery_uninstall';
    const EVENT_TYPE_CALLBACK_KEY_INSTALL = 'callback_key_install';

    const EVENT_TYPE_WIZARD_STEP_1_COMPLETED  = 'wizard_step_1_completed';
    const EVENT_TYPE_WIZARD_STEP_2_COMPLETED  = 'wizard_step_2_completed';
    const EVENT_TYPE_WIZARD_STEP_3_COMPLETED  = 'wizard_step_3_completed';
    const EVENT_TYPE_WIZARD_STEP_4_COMPLETED  = 'wizard_step_4_completed';
    const EVENT_TYPE_WIZARD_STEP_5_COMPLETED  = 'wizard_step_5_completed';
    const EVENT_TYPE_WIZARD_STEP_6_COMPLETED  = 'wizard_step_6_completed';

    /** @var string */
    private $eventType;

    /** @var string|null */
    private $eventDatetime;

    /** @var string */
    private $siteUrl;

    /** @var string */
    private $cmsName;

    public function __construct(string $eventType, string $siteUrl, string $eventDatetime, string $cmsName = 'WooCommerce')
    {
        $this->eventType     = $eventType;
        $this->siteUrl       = $siteUrl;
        $this->eventDatetime = $eventDatetime;
        $this->cmsName       = $cmsName;
    }

    public function getRequestData(): array
    {
        $data = [
            'event_type'     => $this->eventType,
            'event_datetime' => $this->eventDatetime ? date('c', strtotime($this->eventDatetime)) : date('c'),
            'site_url'       => $this->siteUrl,
            'cms_name'       => $this->cmsName,
        ];

        return $data;
    }
}
