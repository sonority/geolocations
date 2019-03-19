<?php

namespace Sonority\Geolocations\Domain\Model;

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

/**
 * Location
 */
class Location extends \TYPO3\CMS\Extbase\DomainObject\AbstractEntity
{

    /**
     * Timestamp
     *
     * @var \DateTime
     */
    protected $tstamp;

    /**
     * Creation date
     *
     * @var \DateTime
     */
    protected $crdate;

    /**
     * Creation user ID
     *
     * @var int
     */
    protected $cruserId;

    /**
     * @var int
     */
    protected $sysLanguageUid;

    /**
     * @var int
     */
    protected $l10nParent;

    /**
     * Deleted
     *
     * @var bool
     */
    protected $deleted;

    /**
     * Hidden
     *
     * @var bool
     */
    protected $hidden;

    /**
     * Starttime
     *
     * @var \DateTime
     */
    protected $starttime;

    /**
     * Endtime
     *
     * @var \DateTime
     */
    protected $endtime;

    /**
     * FE-group
     *
     * @var string
     */
    protected $feGroup;

    /**
     * Title
     *
     * @var string
     */
    protected $title;

    /**
     * Bodytext
     *
     * @var string
     */
    protected $bodytext;

    /**
     * Image
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @lazy
     */
    protected $image;

    /**
     * Marker
     *
     * @var \TYPO3\CMS\Extbase\Domain\Model\FileReference
     * @lazy
     */
    protected $marker;

    /**
     * Latitude
     *
     * @var string
     */
    protected $latitude;

    /**
     * Longitude
     *
     * @var string
     */
    protected $longitude;

    /**
     * Place ID
     *
     * @var string
     */
    protected $placeId;

    /**
     * Address
     *
     * @var string
     */
    protected $address;

    /**
     * Zip
     *
     * @var string
     */
    protected $zip;

    /**
     * City
     *
     * @var string
     */
    protected $city;

    /**
     * Country
     *
     * @var \SJBR\StaticInfoTables\Domain\Model\CountryZone
     * @lazy
     */
    protected $zone;

    /**
     * Country
     *
     * @var \SJBR\StaticInfoTables\Domain\Model\Country
     * @lazy
     */
    protected $country;

    /**
     * www
     *
     * @var string
     */
    protected $www;

    /**
     * E-Mail
     *
     * @var string
     */
    protected $email;

    /**
     * Phone
     *
     * @var string
     */
    protected $phone;

    /**
     * Status
     *
     * @var int
     */
    protected $status = 0;

    /**
     * Datetime
     *
     * @var \DateTime
     */
    protected $datetime;

    /**
     * FE-user
     *
     * @var string
     */
    protected $feUser;

    /**
     * Categories
     *
     * @var \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Sonority\Geolocations\Domain\Model\Category>
     * @lazy
     */
    protected $categories = null;

    /**
     * Distance
     *
     * @var float
     */
    protected $distance;

    /**
     * Map icon
     *
     * @var array
     * @transient
     */
    protected $mapIcon;

    /**
     * __construct
     */
    public function __construct()
    {
        $this->initStorageObjects();
    }

    /**
     * Initializes all ObjectStorage properties
     *
     * @return void
     */
    protected function initStorageObjects()
    {
        $this->categories = new \TYPO3\CMS\Extbase\Persistence\ObjectStorage();
    }

    /**
     * Get timestamp
     *
     * @return int
     */
    public function getTstamp()
    {
        return $this->tstamp;
    }

    /**
     * Set time stamp
     *
     * @param int $tstamp time stamp
     * @return void
     */
    public function setTstamp($tstamp)
    {
        $this->tstamp = $tstamp;
    }

    /**
     * Get creation date
     *
     * @return int
     */
    public function getCrdate()
    {
        return $this->crdate;
    }

    /**
     * Set creation date
     *
     * @param int $crdate
     * @return void
     */
    public function setCrdate($crdate)
    {
        $this->crdate = $crdate;
    }

    /**
     * Get id of creator user
     *
     * @return int
     */
    public function getCruserId()
    {
        return $this->cruserId;
    }

    /**
     * Set cruser id
     *
     * @param int $cruserId id of creator user
     * @return void
     */
    public function setCruserId($cruserId)
    {
        $this->cruserId = $cruserId;
    }

    /**
     * Set sys language
     *
     * @param int $sysLanguageUid
     * @return void
     */
    public function setSysLanguageUid($sysLanguageUid)
    {
        $this->_languageUid = $sysLanguageUid;
    }

    /**
     * Get sys language
     *
     * @return int
     */
    public function getSysLanguageUid()
    {
        return $this->_languageUid;
    }

    /**
     * Set l10n parent
     *
     * @param int $l10nParent
     * @return void
     */
    public function setL10nParent($l10nParent)
    {
        $this->l10nParent = $l10nParent;
    }

    /**
     * Get l10n parent
     *
     * @return int
     */
    public function getL10nParent()
    {
        return $this->l10nParent;
    }

    /**
     * Get deleted flag
     *
     * @return int
     */
    public function getDeleted()
    {
        return $this->deleted;
    }

    /**
     * Set deleted flag
     *
     * @param int $deleted deleted flag
     * @return void
     */
    public function setDeleted($deleted)
    {
        $this->deleted = $deleted;
    }

    /**
     * Get hidden flag
     *
     * @return int
     */
    public function getHidden()
    {
        return $this->hidden;
    }

    /**
     * Set hidden flag
     *
     * @param int $hidden hidden flag
     * @return void
     */
    public function setHidden($hidden)
    {
        $this->hidden = $hidden;
    }

    /**
     * Get start time
     *
     * @return \DateTime
     */
    public function getStarttime()
    {
        return $this->starttime;
    }

    /**
     * Set start time
     *
     * @param int $starttime start time
     * @return void
     */
    public function setStarttime($starttime)
    {
        $this->starttime = $starttime;
    }

    /**
     * Get endtime
     *
     * @return \DateTime
     */
    public function getEndtime()
    {
        return $this->endtime;
    }

    /**
     * Set end time
     *
     * @param int $endtime end time
     * @return void
     */
    public function setEndtime($endtime)
    {
        $this->endtime = $endtime;
    }

    /**
     * Get fe groups
     *
     * @return string
     */
    public function getFeGroup()
    {
        return $this->feGroup;
    }

    /**
     * Set fe group
     *
     * @param string $feGroup comma separated list
     * @return void
     */
    public function setFeGroup($feGroup)
    {
        $this->feGroup = $feGroup;
    }

    /**
     * Returns the title
     *
     * @return string $title
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Sets the title
     *
     * @param string $title
     * @return void
     */
    public function setTitle($title)
    {
        $this->title = $title;
    }

    /**
     * Get bodytext
     *
     * @return string
     */
    public function getBodytext()
    {
        return $this->bodytext;
    }

    /**
     * Set bodytext
     *
     * @param string $bodytext Content
     * @return void
     */
    public function setBodytext($bodytext)
    {
        $this->bodytext = $bodytext;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function getImage()
    {
        return $this->image;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $image
     */
    public function setImage($image)
    {
        $this->image = $image;
    }

    /**
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $marker
     */
    public function getMarker()
    {
        return $this->marker;
    }

    /**
     * @param \TYPO3\CMS\Extbase\Domain\Model\FileReference $marker
     */
    public function setMarker($marker)
    {
        $this->marker = $marker;
    }

    /**
     * Returns the latitude
     *
     * @return string $latitude
     */
    public function getLatitude()
    {
        return $this->latitude;
    }

    /**
     * Sets the latitude
     *
     * @param string $latitude
     * @return void
     */
    public function setLatitude($latitude)
    {
        $this->latitude = $latitude;
    }

    /**
     * Returns the longitude
     *
     * @return string $longitude
     */
    public function getLongitude()
    {
        return $this->longitude;
    }

    /**
     * Sets the longitude
     *
     * @param string $longitude
     * @return void
     */
    public function setLongitude($longitude)
    {
        $this->longitude = $longitude;
    }

    /**
     * Returns the place ID
     *
     * @return string $placeId
     */
    public function getPlaceId()
    {
        return $this->placeId;
    }

    /**
     * Sets the place ID
     *
     * @param string $placeId
     * @return void
     */
    public function setPlaceId($placeId)
    {
        $this->placeId = $placeId;
    }

    /**
     * Returns the address
     *
     * @return string $address
     */
    public function getAddress()
    {
        return $this->address;
    }

    /**
     * Sets the address
     *
     * @param string $address
     * @return void
     */
    public function setAddress($address)
    {
        $this->address = $address;
    }

    /**
     * Returns the zip
     *
     * @return string $zip
     */
    public function getZip()
    {
        return $this->zip;
    }

    /**
     * Sets the zip
     *
     * @param string $zip
     * @return void
     */
    public function setZip($zip)
    {
        $this->zip = $zip;
    }

    /**
     * Returns the city
     *
     * @return string $city
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Sets the city
     *
     * @param string $city
     * @return void
     */
    public function setCity($city)
    {
        $this->city = $city;
    }

    /**
     * Returns the zone
     *
     * @return \SJBR\StaticInfoTables\Domain\Model\CountryZone $zone
     */
    public function getZone()
    {
        return $this->zone;
    }

    /**
     * Returns the zone
     *
     * @return \SJBR\StaticInfoTables\Domain\Model\CountryZone $zone
     */
    public function getZoneName()
    {
        return $this->zone->getLocalName();
    }

    /**
     * Sets the zone
     *
     * @param \SJBR\StaticInfoTables\Domain\Model\CountryZone $zone
     * @return void
     */
    public function setZone($zone)
    {
        $this->zone = $zone;
    }

    /**
     * Returns the country
     *
     * @return \SJBR\StaticInfoTables\Domain\Model\Country $country
     */
    public function getCountryName()
    {
        return $this->country->getShortNameLocal();
    }

    /**
     * Returns the country
     *
     * @return \SJBR\StaticInfoTables\Domain\Model\Country $country
     */
    public function getCountry()
    {
        return $this->country;
    }

    /**
     * Sets the country
     *
     * @param \SJBR\StaticInfoTables\Domain\Model\Country $country
     * @return void
     */
    public function setCountry($country)
    {
        $this->country = $country;
    }

    /**
     * Returns the www
     *
     * @return string $www
     */
    public function getWww()
    {
        return $this->www;
    }

    /**
     * Sets the www
     *
     * @param string $www
     * @return void
     */
    public function setWww($www)
    {
        $this->www = $www;
    }

    /**
     * Returns the email
     *
     * @return string $email
     */
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Sets the email
     *
     * @param string $email
     * @return void
     */
    public function setEmail($email)
    {
        $this->email = $email;
    }

    /**
     * Returns the phone
     *
     * @return string $phone
     */
    public function getPhone()
    {
        return $this->phone;
    }

    /**
     * Sets the phone
     *
     * @param string $phone
     * @return void
     */
    public function setPhone($phone)
    {
        $this->phone = $phone;
    }

    /**
     * Returns the status
     *
     * @return integer $status
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * Sets the status
     *
     * @param integer $status
     * @return void
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * Gets the datetime
     *
     * @return \DateTime
     */
    public function getDatetime()
    {
        return $this->datetime;
    }

    /**
     * Sets the datetime
     *
     * @param \DateTime $datetime datetime
     * @return void
     */
    public function setDatetime($datetime)
    {
        $this->datetime = $datetime;
    }

    /**
     * Get fe user
     *
     * @return string
     */
    public function getFeUser()
    {
        return $this->feUser;
    }

    /**
     * Set fe user
     *
     * @param string $feUser comma separated list
     * @return void
     */
    public function setFeUser($feUser)
    {
        $this->feUser = $feUser;
    }

    /**
     * Returns the Categories
     *
     * @return \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Sonority\Geolocations\Domain\Model\Category> $categories
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * Sets the Categories
     *
     * @param \TYPO3\CMS\Extbase\Persistence\ObjectStorage<\Sonority\Geolocations\Domain\Model\Category> $categories
     * @return void
     */
    public function setCategories(\TYPO3\CMS\Extbase\Persistence\ObjectStorage $categories)
    {
        $this->categories = $categories;
    }

    /**
     * Get distance
     *
     * @return float
     */
    public function getDistance()
    {
        return $this->distance;
    }

    /**
     * Set distance
     *
     * @param float $distance
     * @return void
     */
    public function setDistance($lat1, $lon1, $kilometer = true)
    {
        $lat2 = $this->getLatitude();
        $lon2 = $this->getLongitude();
        if ($kilometer) {
            $earthRadius = 6371;
        } else {
            $earthRadius = 3958.75;
        }
        $rad = M_PI / 180;
        $this->distance = acos(
                sin($lat2 * $rad) * sin($lat1 * $rad) + cos($lat2 * $rad) * cos($lat1 * $rad) * cos($lon2 * $rad - $lon1 * $rad)
            ) * $earthRadius;
    }

    /**
     * Set map-icon
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $mapIcon
     * @return void
     */
    public function setMapIcon($mapIcon)
    {
        $this->mapIcon = $mapIcon;
    }

    /**
     * Get map-icon
     *
     * @return \TYPO3\CMS\Extbase\Domain\Model\FileReference $mapIcon
     */
    public function getMapIcon()
    {
        $markerElement = $this->getMarker();

        if (!is_null($markerElement)) {
            return $markerElement;
        } else {
            $categories = $this->getCategories();
            foreach ($categories as $category) {
                $categoryMarker = $category->getMarker();
            }
            if (!is_null($categoryMarker)) {
                return $categoryMarker;
            }
        }
        return null;
    }

}
