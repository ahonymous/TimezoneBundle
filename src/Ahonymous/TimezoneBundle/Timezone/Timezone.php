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
     * @param Client      $client
     * @param YamlStorage $yaml
     */
    public function __construct($apiKey, Client $client, YamlStorage $yaml)
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
     * @param $timezoneKey
     * @throws \Exception
     */
    public function getTimeZoneName($timezoneKey)
    {
        $timezone = $this->yaml->getRecord($timezoneKey);
        if ($timezone) {
            return array_key_exists('timeZoneName', $timezone) ? $timezone['timeZoneName'] : $timezoneKey;
        }
        $location = $this->getDateTimeZone($timezoneKey)->getLocation();

        $data = self::getGoogleTimezoneData(
            $this->http,
            implode(',', array_intersect_key($location, ['latitude' => 0, 'longitude' => 0])),
            time(),
            $this->apiKey
        );

        $data['location'] = $location;

        if ($data['status'] == 'OK' || $data['status'] == 'ZERO_RESULTS') {
            $this->yaml->addRecord($timezoneKey, $data);

            return array_key_exists('timeZoneName', $data) ? $data['timeZoneName'] : $timezoneKey;
        } else {
            throw new \Exception("response status " . $data['status']);
        }
    }

    /**
     * @param  Client $client
     * @param $location
     * @param $timestamp
     * @param $apiKey
     * @return mixed
     */
    public static function getGoogleTimezoneData(Client $client, $location, $timestamp, $apiKey)
    {
        $uri = sprintf("/maps/api/timezone/json?location=%s&timestamp=%d&key=%s",$location, $timestamp, $apiKey);

        return json_decode($client->get($uri)->send()->getBody(), true);
    }
}
