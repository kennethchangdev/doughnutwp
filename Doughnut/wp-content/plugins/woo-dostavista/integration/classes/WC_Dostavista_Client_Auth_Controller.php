<?php

use WooDostavista\DostavistaAuth\DostavistaClientManager;
use WooDostavista\View\JsonResponse;

defined('ABSPATH') || exit;

class WC_Dostavista_Client_Auth_Controller
{
    public static function logout_dostavista()
    {
        DostavistaClientManager::logoutFromDostavista();

        if(true) {
            (new JsonResponse(
                [
                    'is_success' => true,
                ]
            ))->render();
        } else {
            (new JsonResponse(['error' => 'invalid_client'], 400))->render();
        }
    }
}
