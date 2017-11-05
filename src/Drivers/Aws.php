<?php

namespace Matthewbdaly\SMS\Drivers;

use Matthewbdaly\SMS\Contracts\Driver;
use Aws\Sns\SnsClient;

/**
 * Driver for AWS SNS.
 */
class Aws implements Driver
{
    /**
     * Guzzle response.
     *
     * @var
     */
    protected $response;

    /**
     * Endpoint.
     *
     * @var
     */
    private $endpoint = '';

    /**
     * SNS Client
     *
     * @var
     */
    protected $sns;

    /**
     * Constructor.
     *
     * @param array          $config The configuration array.
     * @param SnsClient|null $sns    The Amazon SNS client.
     *
     * @return void
     */
    public function __construct(array $config = [], SnsClient $sns = null)
    {
        if (!$sns) {
            $params = array(
                'credentials' => array(
                    'key' => $config['api_key'],
                    'secret' => $config['api_secret']
                ),
                'region' => $config['api_region'],
                'version' => 'latest'
            );
            $sns = new SnsClient($params);
        }
        $this->sns = $sns;
    }

    /**
     * Get driver name.
     *
     * @return string
     */
    public function getDriver(): string
    {
        return 'Aws';
    }

    /**
     * Get endpoint URL.
     *
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * Send the SMS.
     *
     * @param array $message An array containing the message.
     *
     * @throws \Matthewbdaly\SMS\Exceptions\ClientException  Client exception.
     *
     * @return boolean
     */
    public function sendRequest(array $message): bool
    {
        try {
            $args = array(
                "SenderID" => $message['from'],
                "SMSType" => "Transactional",
                "Message" => $message['content'],
                "PhoneNumber" => $message['to']
            );

            $this->sns->publish($args);
        } catch (\Aws\Sns\Exception\SnsException $e) {
            throw new \Matthewbdaly\SMS\Exceptions\ClientException();
        }

        return true;
    }
}
