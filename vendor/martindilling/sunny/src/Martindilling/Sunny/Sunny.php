<?php namespace Martindilling\Sunny;

use Carbon\Carbon;

/**
 * Extends Carbon with possibility for getting sunrise and sunset
 * TODO: Check if php actually does this right: http://williams.best.vwh.net/sunrise_sunset_algorithm.htm
 *
 * @property      float   $latitude
 * @property      float   $longitude
 * @property      float   $zenith
 * @property-read string  $sunrise
 * @property-read string  $sunset
 * @property-read string  $sunnyMinutes
 * @property-read string  $sunnyTime
 *
 */
class Sunny extends Carbon {

    /**
     * Latitude used for finding sunrise and sunset
     * 
     * @var float
     */
    protected $latitude;

    /**
     * Longitude used for finding sunrise and sunset
     * 
     * @var float
     */
    protected $longitude;

    /**
     * Zenith used for finding sunrise and sunset
     * Note: Being set correctly in constructor, as the php default is wrong
     * 
     * @var float
     */
    protected $zenith;

    /**
     * The format to return sunrise and sunset in
     * 
     * From php.net docs:
     * SUNFUNCS_RET_STRING     returns the result as string               '16:46'
     * SUNFUNCS_RET_DOUBLE     returns the result as float                16.78243132
     * SUNFUNCS_RET_TIMESTAMP  returns the result as integer (timestamp)  1095034606
     * 
     * @var SUNFUNCS_RET_STRING|SUNFUNCS_RET_DOUBLE|SUNFUNCS_RET_TIMESTAMP
     */
    protected $sunfuncFormat = SUNFUNCS_RET_STRING;


    ///////////////////////////////////////////////////////////////////
    //////////////////////////// CONSTRUCTORS /////////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Create a new Sunny instance.
     *
     * @param string               $time
     * @param DateTimeZone|string  $tz
     * @param float                $latitude
     * @param float                $longitude
     */
    public function __construct($time = null, $tz = null, $latitude = null, $longitude = null)
    {
        parent::__construct($time, $tz);

        $this->setLocation($latitude, $longitude);

        /**
         * According to these sources default date.sunrise_zenith and
         * date.sunset_zenith in php is wrong. So fixing it here.
         * https://bugs.php.net/bug.php?id=49448
         * http://aa.usno.navy.mil/faq/docs/RST_defs.php
         */
         $this->zenith = 90 + (50/60);
    }


    ///////////////////////////////////////////////////////////////////
    ///////////////////////// GETTERS AND SETTERS /////////////////////
    ///////////////////////////////////////////////////////////////////

    /**
     * Get a part of the Sunny object
     *
     * @param  string $name
     *
     * @throws InvalidArgumentException
     *
     * @return float|string
     */
    public function __get($name)
    {
        switch ($name) {
            case 'latitude':
                return (float) $this->latitude;

            case 'longitude':
                return (float) $this->longitude;

            case 'zenith':
                return (float) $this->zenith;
            
            case 'sunrise':
                return $this->getSunrise();

            case 'sunset':
                return $this->getSunset();
            
            case 'sunnyMinutes':
                return $this->getSunnyMinutes();

            case 'sunnyTime':
                return $this->getSunnyTime();
        }

        return parent::__get($name);
    }

    /**
     * Get time for the sunrise
     *
     * @param int  $format
     * @return string|float|integer
     */
    public function getSunrise($format = null)
    {
        $format = $format !== null ? $format : $this->sunfuncFormat;

        return date_sunrise(
            $this->timestamp,
            $format,
            $this->latitude ?: ini_get("date.default_latitude"),
            $this->longitude ?: ini_get("date.default_longitude"),
            $this->zenith,
            $this->offsetHours
        );
    }

    /**
     * Get time for the sunset
     *
     * @param int  $format
     * @return string|float|integer
     */
    public function getSunset($format = null)
    {
        $format = $format !== null ? $format : $this->sunfuncFormat;

        return date_sunset(
            $this->timestamp,
            $format,
            $this->latitude ?: ini_get("date.default_latitude"),
            $this->longitude ?: ini_get("date.default_longitude"),
            $this->zenith,
            $this->offsetHours
        );
    }

    /**
     * Get minutes the sun is up
     *
     * @return int
     */
    public function getSunnyMinutes()
    {
        $sunrise = self::createFromTimestamp($this->getSunrise(SUNFUNCS_RET_TIMESTAMP));
        $sunset = self::createFromTimestamp($this->getSunset(SUNFUNCS_RET_TIMESTAMP));

        return $sunrise->diffInMinutes($sunset);
    }

    /**
     * Get hours:minutes the sun is up
     *
     * @param string $format
     * @return string
     */
    public function getSunnyTime($format = '%d:%02d')
    {
        $sunnyMinutes = $this->getSunnyMinutes();

        $hours = floor($sunnyMinutes / 60);
        $minutes = ($sunnyMinutes % 60);

        return sprintf($format, $hours, $minutes);
    }

    /**
     * Set the instance's latitude
     *
     * @param  float $value
     *
     * @return Sunny
     */
    public function latitude($value)
    {
        $this->latitude = $value;

        return $this;
    }

    /**
     * Set the instance's longitude
     *
     * @param  float $value
     *
     * @return Sunny
     */
    public function longitude($value)
    {
        $this->longitude = $value;

        return $this;
    }

    /**
     * Set the location all together
     *
     * @param  float $latitude
     * @param  float $longitude
     *
     * @return Sunny
     */
    public function setLocation($latitude, $longitude)
    {
        return $this->latitude($latitude)->longitude($longitude);
    }

    /**
     * Set the instance's zenith
     *
     * @param  float $value
     *
     * @return Sunny
     */
    public function zenith($value)
    {
        $this->zenith = $value;

        return $this;
    }
}
