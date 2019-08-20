# Sunny

Extension of the great library [Carbon](https://github.com/briannesbitt/Carbon) 
([composer package](https://packagist.org/packages/nesbot/carbon)).  
Added functionality to get information related to sunrise and sunset for a specified location.


### Install with Composer

```json
{
    "require": {
        "martindilling/sunny": "0.*"
    }
}
```

```php
<?php
require 'vendor/autoload.php';

use Martindilling\Sunny\Sunny;

printf("Now: %s", Sunny::now());
```

### Example

You can use it as you would normally use Carbon, but with some extra functionallity:

```php
$day = new Martindilling\Sunny\Sunny('2014-04-25', 'Europe/Copenhagen');
$day->setLocation(56.4618773, 10.0194839);

echo "Date:                 ".$day."<br>\n";
echo "Location:             ".$day->latitude.", ".$day->longitude."<br>\n";
echo "Zenith:               ".$day->zenith."<br>\n";
echo "<br>\n";
echo "Sunrise:              ".$day->sunrise."<br>\n";
echo "Sunset:               ".$day->sunset."<br>\n";
echo "Sun is up for         ".$day->sunnyMinutes." minutes<br>\n";
echo "Sun is up for:        ".$day->sunnyTime."<br>\n";

echo "Sunrise as string:    ".$day->getSunrise(SUNFUNCS_RET_STRING)."<br>\n";
echo "Sunrise as float:     ".$day->getSunrise(SUNFUNCS_RET_DOUBLE)."<br>\n";
echo "Sunrise as timestamp: ".$day->getSunrise(SUNFUNCS_RET_TIMESTAMP)."<br>\n";
echo "<br>\n";
echo "Sunset as string:     ".$day->getSunset(SUNFUNCS_RET_STRING)."<br>\n";
echo "Sunset as float:      ".$day->getSunset(SUNFUNCS_RET_DOUBLE)."<br>\n";
echo "Sunset as timestamp:  ".$day->getSunset(SUNFUNCS_RET_TIMESTAMP)."<br>\n";
echo "<br>\n";
echo "Sunny time format:    ".$day->getSunnyTime('%d:%d')."<br>\n";
echo "Sunny time format:    ".$day->getSunnyTime('%d:%02d')."<br>\n";
echo "Sunny time format:    ".$day->getSunnyTime('%04d:%03d')."<br>\n";

/**
 * Date:                 2014-04-25 00:00:00
 * Location:             56.4618773, 10.0194839
 * Zenith:               90.833333333333
 * 
 * Sunrise:              05:47
 * Sunset:               20:48
 * Sun is up for         901 minutes
 * Sun is up for:        15:01
 * Sunrise as string:    05:47
 * Sunrise as float:     5.7847680240886
 * Sunrise as timestamp: 1398311225
 * 
 * Sunset as string:     20:48
 * Sunset as float:      20.81488621949
 * Sunset as timestamp:  1398365333
 * 
 * Sunny time format:    15:1
 * Sunny time format:    15:01
 * Sunny time format:    0015:001
 */

```

### Author

Martin Dilling-Hansen - martindilling@gmail.com - martindilling.com

### License

Sunny is licensed under the MIT License
