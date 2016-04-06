<?php

namespace Sonority\Geolocations\Service;

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * calculate the geo coordinates of an address, using the googe geocoding
 * API, an API key is needed, as this is a server-side process.
 */
class GeocodeService
{

    /**
     *
     * @var string
     */
    protected $apikey = '';

    /**
     *
     * @var integer
     */
    protected $cacheTime = 7776000;

    /**
     * Base URL to fetch the coordinates (latitude, longitude of a address string).
     */
    protected $geocodingUrl = 'https://maps.googleapis.com/maps/api/geocode/json?sensor=false';

    /**
     * Sets the google maps API-key and the language
     *
     * @param string $apikey (optional) the API key from google, if empty, the default from the configuration is taken
     * @param string $language
     */
    public function __construct($apikey = null, $language = null)
    {
        // Get extensions configuration
        if ($apikey === null) {
            $extConf = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['geolocations']);
            if (!empty($extConf['apiKey'])) {
                $this->apikey = $extConf['apiKey'];
                $this->geocodingUrl .= '&key=' . $extConf['apiKey'];
            }
        }
        if ($language !== null) {
            $this->geocodingUrl .= '&language=' . $language;
        }
    }

    /**
     * Reverse geocode addresses to coordinates
     *
     * @param $address
     *
     * @return array an array with latitude, longitude and place_id
     */
    public function getCoordinatesForAddress($address = '')
    {
        $results = null;
        $address = trim($address);

        if ($address) {
            $cacheObject = $this->initializeCache();
            // Create the cache key
            $cacheKey = 'geolocations-' . strtolower(str_replace(' ', '-', preg_replace('/[^0-9a-zA-Z ]/m', '', $address)));
            // Not in cache yet
            if (!$cacheObject->has($cacheKey)) {
                $geocodingUrl = $this->geocodingUrl . '&address=' . urlencode($address);
                $results = json_decode(GeneralUtility::getUrl($geocodingUrl, true));

                if (count($results['results']) > 0) {
                    $record = reset($results['results']);
                    $results = array(
                        'latitude' => $record['geometry']['location']['lat'],
                        'longitude' => $record['geometry']['location']['lng'],
                        'place_id' => $record['place_id'] ? $record['place_id'] : ''
                    );
                    // Store the result in cache and return
                    $cacheObject->set($cacheKey, $results, array(), $this->cacheTime);
                }
                sleep(0.5);
            } else {
                $results = $cacheObject->get($cacheKey);
            }
        }

        return $results;
    }

    /**
     * Initializes the cache for the DB requests.
     *
     * @return Cache Object
     */
    protected function initializeCache()
    {
        try {
            return GeneralUtility::makeInstance('TYPO3\\CMS\\Core\\Cache\\CacheManager')->getCache('cache_geolocations_address');
        } catch (\TYPO3\CMS\Core\Cache\Exception\NoSuchCacheException $e) {
            throw new \RuntimeException('Unable to load Cache! 1299944198');
        }
    }

}
