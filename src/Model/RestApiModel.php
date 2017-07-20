<?php

namespace ZuluCrypto\StellarSdk\Model;

use ZuluCrypto\StellarSdk\Horizon\ApiClient;

class RestApiModel
{
    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * @return ApiClient
     */
    public function getApiClient()
    {
        return $this->apiClient;
    }

    /**
     * @param ApiClient $apiClient
     */
    public function setApiClient($apiClient)
    {
        $this->apiClient = $apiClient;
    }
}