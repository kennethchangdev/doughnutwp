<?php

namespace WooDostavista\DvCmsModuleApiClient\Response;

use DateTime;

class SdekPointResponseModel
{
    const POINT_TYPE = 'sdek';

    /** @var array */
    private $responsePointData;

    /** @var string */
    private $expectedStartDatetime;

    /** @var string */
    private $expectedFinishDatetime;

    public function __construct(array $responsePointData)
    {
        $this->responsePointData = $responsePointData;

        $this->expectedStartDatetime  = $responsePointData['expected_start_datetime'] ?? '';
        $this->expectedFinishDatetime = $responsePointData['expected_finish_datetime'] ?? '';
    }

    /**
     * @param int|null $time
     * @return int|null
     */
    public function getDeliveryIntervalMinDays(int $time = null)
    {
        if ($this->expectedStartDatetime) {
            $startDatetime = new DateTime($this->expectedStartDatetime);
            return (int) $startDatetime->diff(new DateTime(date('c', $time ?? time())))->days;
        }
        return null;
    }

    /**
     * @param int|null $time
     * @return int|null
     */
    public function getDeliveryIntervalMaxDays(int $time = null)
    {
        if ($this->expectedFinishDatetime) {
            $finishDatetime = new DateTime($this->expectedFinishDatetime);
            return (int) (new DateTime(date('c', $time ?? time())))->diff($finishDatetime)->days;
        }
        return null;
    }
}
