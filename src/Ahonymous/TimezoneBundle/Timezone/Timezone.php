<?php

namespace Ahonymous\TimezoneBundle\Timezone;

use Ahonymous\TimezoneBundle\Storage\YamlStorage;
use Guzzle\Http\Client;

class Timezone
{
    /**
     * @var string
     */
    private $apiKey;

    /**
     * @var Client
     */
    private $http;

    /**
     * @var YamlStorage
     */
    private $yaml;

    /**
     * @param $apiKey
     * @param Client $client
     * @param YamlStorage $yaml
     */
    function __construct($apiKey, Client $client, YamlStorage $yaml)
    {
        $this->apiKey = $apiKey;
        $this->http = $client;
        $this->yaml = $yaml;
    }

    /**
     * @param $key
     * @return \DateTimeZone
     */
    public function getDateTimeZone($key)
    {
        return new \DateTimeZone($key);
    }

    /**
     * @param \DateTimeZone $timeZone
     * @return string
     */
    public function getLocation(\DateTimeZone $timeZone)
    {
        return implode(',', array_intersect_key($timeZone->getLocation(), ['latitude' => 0, 'longitude' => 0]));
    }

    /**
     * @param $timezoneKey
     * @throws \Exception
     */
    public function getTimeZoneName($timezoneKey)
    {
        $timezone = $this->yaml->getRecord($timezoneKey);
        if ($timezone) {
            return array_key_exists('timeZoneName', $timezone) ? $timezone['timeZoneName'] : $timezoneKey;
        }

        $uri = "/maps/api/timezone/json?location=" . $this->getLocation($this->getDateTimeZone($timezoneKey)) . "&timestamp=" . time() . "&key=" . $this->apiKey;
        $data = json_decode($this->http->get($uri)->send()->getBody(), true);

        if ($data['status'] == 'OK' || $data['status'] == 'ZERO_RESULTS') {
            $this->yaml->addRecord($timezoneKey, $data);

            return array_key_exists('timeZoneName', $data) ? $data['timeZoneName'] : $timezoneKey;
        } else {
            throw new \Exception("response status " . $data['status']);
        }
    }
}
