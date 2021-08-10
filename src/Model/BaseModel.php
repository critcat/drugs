<?php

namespace App\Model;

use App\Service\ApiRequester;

class BaseModel
{
    protected ApiRequester $apiRequester;

    public function __construct(ApiRequester $apiRequester)
    {
        $this->apiRequester = $apiRequester;
    }
}