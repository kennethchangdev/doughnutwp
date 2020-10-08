<?php

namespace WooDostavista\DvCmsModuleApiClient\Response;

use WooDostavista\BankCard\BankCard;

class BankCardsResponseModel
{
    /** @var BankCard[] */
    private $cards;

    public function __construct(array $responseBankCardsData)
    {
        foreach ($responseBankCardsData['bank_cards'] as $cardData) {
            $this->cards[] = new BankCard(
                $cardData['bank_card_id'],
                $cardData['bank_card_number_mask'],
                $cardData['expiration_date'],
                $cardData['is_last_used'],
                $cardData['card_type']
            );
        }
    }

    /**
     * @return BankCard[]
     */
    public function getCards(): array
    {
        return $this->cards ?? [];
    }
}