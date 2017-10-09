<?php

namespace ZuluCrypto\StellarSdk\Model;

use ZuluCrypto\StellarSdk\Horizon\ApiClient;

class RestApiModel
{
    /**
     * ID within the Stellar network
     *
     * @var string
     */
    protected $id;

    /**
     * Array of links to other objects that can be retrieved via the REST API
     *
     * @var array
     */
    protected $links;

    /**
     * Paging token for iterating within a stream
     *
     * @var string
     */
    protected $pagingToken;

    /**
     * @var ApiClient
     */
    protected $apiClient;

    /**
     * The raw response json
     *
     * @var array
     */
    protected $rawData;

    public function loadFromRawResponseData($rawData)
    {
        $this->rawData = $rawData;

        if (isset($rawData['_links'])) $this->links = $rawData['_links'];
        if (isset($rawData['id'])) $this->id = $rawData['id'];
    }

    /**
     * @return string
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

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

    /**
     * @return array
     */
    public function getLinks()
    {
        return $this->links;
    }

    /**
     * @param array $links
     */
    public function setLinks($links)
    {
        $this->links = $links;
    }

    /**
     * @return string
     */
    public function getPagingToken()
    {
        return $this->pagingToken;
    }

    /**
     * @param string $pagingToken
     */
    public function setPagingToken($pagingToken)
    {
        $this->pagingToken = $pagingToken;
    }

    /**
     * @return array
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param array $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }
}