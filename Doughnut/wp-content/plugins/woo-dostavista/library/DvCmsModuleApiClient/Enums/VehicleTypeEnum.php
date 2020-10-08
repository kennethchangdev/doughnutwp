<?php

namespace WooDostavista\DvCmsModuleApiClient\Enums;

use WC_Dostavista_Lang;

class VehicleTypeEnum
{
    public static function getEnum(): array
    {
        return [
            6 => WC_Dostavista_Lang::getHtml('vehicle_type_enum_walk'),
            7 => WC_Dostavista_Lang::getHtml('vehicle_type_enum_car'),
            1 => WC_Dostavista_Lang::getHtml('vehicle_type_enum_truck_pickup'),
            2 => WC_Dostavista_Lang::getHtml('vehicle_type_enum_truck_minivan'),
            3 => WC_Dostavista_Lang::getHtml('vehicle_type_enum_truck_porter'),
            4 => WC_Dostavista_Lang::getHtml('vehicle_type_enum_truck_van'),
            8 => WC_Dostavista_Lang::getHtml('vehicle_type_enum_motorbike'),
        ];
    }
}
