<?php

namespace Happyr\GoogleAnalyticsBundle\Http;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;

/**
 * @author Tobias Nyholm <tobias.nyholm@gmail.com>
 */
class HttpClient implements HttpClientInterface
{
    /**
     * @var string endpoint
     *
     * Where to POST the requests
     */
    protected $endpoint;

    /**
     * @var int requestTimeout
     */
    protected $requestTimeout;

    /**
     * @var bool fireAndForget
     *
     * Should we bother about the response or not?
     */
    protected $fireAndForget;

    /**
     * @var Client client
     */
    protected $client;

    /**
     * @param string $endpoint
     * @param bool   $fireAndForget
     * @param int    $requestTimeout
     */
    public function __construct($endpoint, $fireAndForget, $requestTimeout)
    {
        $this->endpoint = $endpoint;
        $this->fireAndForget = $fireAndForget;
        $this->requestTimeout = $requestTimeout;
    }

    /**
     * Get a GuzzleClient.
     *
     * @return Client
     */
    protected function getClient()
    {
        if ($this->client === null) {
            $this->client = new Client();
        }

        return $this->client;
    }

    /**
     * Send a post request to the endpoint.
     *
     * @param array $data
     *
     * @return bool
     */
    public function send(array $data = array())
    {
        $client = $this->getClient();
        $options = array(
            'body' => $data,
            'headers' => array(
                'User-Agent' => 'happyr-google-analytics/3.0',
            ),
            'timeout' => $this->requestTimeout,
        );

        $request = $client->createRequest('POST', $this->endpoint, $options);

        // If we should send the async or not.
        if ($this->fireAndForget) {
            $client->sendAll(array($request));

            return true;
        }

        try {
            $response = $client->send($request);
        } catch (RequestException $e) {
            return false;
        }

        return $response->getStatusCode() == '200';
    }
}
