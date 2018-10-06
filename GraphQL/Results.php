<?php
/**
 * Created by PhpStorm.
 * User: mostafa
 * Date: 10/7/18
 * Time: 1:54 PM
 */

namespace GraphQl;

use GuzzleHttp\Message\ResponseInterface;

/**
 * Class Result
 *
 * @package GraphQl
 */
class Results
{
    /**
     * @var string
     */
    protected $responseBody;

    /**
     * @var ResponseInterface
     */
    protected $responseObject;

    /**
     * @var array|object
     */
    protected $results;

    /**
     * Result constructor.
     *
     * Receives json response from GraphQL api response and parses it as associative array or nested object accordingly
     *
     * @param ResponseInterface $response
     * @param bool              $asArray
     */
    public function __construct(ResponseInterface $response, $asArray = false)
    {
        $this->responseObject = $response;
        $this->responseBody   = $this->responseObject->getBody()->getContents();
        $this->results        = json_decode($this->responseBody, $asArray);
    }

    /**
     * @param bool $asArray
     */
    public function reformatResults($asArray)
    {
        $this->results = json_decode($this->responseBody, (bool) $asArray);
    }

    /**
     * Returns only parsed data objects in the requested format
     *
     * @return array|object
     */
    public function getData()
    {
        if (is_array($this->results)) {

            return $this->results['data'];
        } else {

            return $this->results->data;
        }
    }

    /**
     * Returns entire parsed results in the requested format
     *
     * @return array|object
     */
    public function getResults()
    {
        return $this->results;
    }

    /**
     * @return string
     */
    public function getResponseBody()
    {
        return $this->responseBody;
    }

    /**
     * @return ResponseInterface
     */
    public function getResponseObject()
    {
        return $this->responseObject;
    }
}