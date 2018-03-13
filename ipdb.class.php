<?php
/* Copyright (C) 2005-2016 IP2Location.com  All Rights Reserved */

namespace IP2Location;

class Database {
const VERSION = '8.0.1';
const FIELD_NOT_SUPPORTED = 'This parameter is unavailable in selected .BIN data file. Please upgrade.';
const FIELD_NOT_KNOWN = 'This parameter is inexistent. Please verify.';
const INVALID_IP_ADDRESS = 'Invalid IP address.';
const COUNTRY_CODE = 1;
const COUNTRY_NAME = 2;
const REGION_NAME = 3;
const CITY_NAME = 4;
const LATITUDE = 5;
const LONGITUDE = 6;
const ISP = 7;
const DOMAIN_NAME = 8;
const ZIP_CODE = 9;
const TIME_ZONE = 10;
const NET_SPEED = 11;
const IDD_CODE = 12;
const AREA_CODE = 13;
const WEATHER_STATION_CODE = 14;
const WEATHER_STATION_NAME = 15;
const MCC = 16;
const MNC = 17;
const MOBILE_CARRIER_NAME = 18;
const ELEVATION = 19;
const USAGE_TYPE = 20;
const COUNTRY = 101;
const COORDINATES = 102;
const IDD_AREA = 103;
const WEATHER_STATION = 104;
const MCC_MNC_MOBILE_CARRIER_NAME = 105;
const ALL = 1001;
const IP_ADDRESS = 1002;
const IP_VERSION = 1003;
const IP_NUMBER = 1004;
const EXCEPTION = 10000;
const EXCEPTION_NO_SHMOP = 10001;
const EXCEPTION_SHMOP_READING_FAILED = 10002;
const EXCEPTION_SHMOP_WRITING_FAILED = 10003;
const EXCEPTION_SHMOP_CREATE_FAILED = 10004;
const EXCEPTION_DBFILE_NOT_FOUND = 10005;
const EXCEPTION_NO_MEMORY = 10006;
const EXCEPTION_NO_CANDIDATES = 10007;
const EXCEPTION_FILE_OPEN_FAILED = 10008;
const EXCEPTION_NO_PATH = 10009;
const FILE_IO = 100001;
const MEMORY_CACHE = 100002;
const SHARED_MEMORY = 100003;
const SHM_PERMS = 0600;
const SHM_CHUNK_SIZE = 524288;
private static $columns = [
self::COUNTRY_CODE => [8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8],
self::COUNTRY_NAME => [8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8, 8],
self::REGION_NAME => [0, 0, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12, 12],
self::CITY_NAME=> [0, 0, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16, 16],
self::LATITUDE => [0, 0, 0, 0, 20, 20, 0, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20, 20],
self::LONGITUDE=> [0, 0, 0, 0, 24, 24, 0, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24, 24],
self::ISP => [0, 12, 0, 20, 0, 28, 20, 28, 0, 32, 0, 36, 0, 36, 0, 36, 0, 36, 28, 36, 0, 36, 28, 36],
self::DOMAIN_NAME => [0, 0, 0, 0, 0, 0, 24, 32, 0, 36, 0, 40, 0, 40, 0, 40, 0, 40, 32, 40, 0, 40, 32, 40],
self::ZIP_CODE => [0, 0, 0, 0, 0, 0, 0, 0, 28, 28, 28, 28, 0, 28, 28, 28, 0, 28, 0, 28, 28, 28, 0, 28],
self::TIME_ZONE=> [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 32, 32, 28, 32, 32, 32, 28, 32, 0, 32, 32, 32, 0, 32],
self::NET_SPEED=> [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 32, 44, 0, 44, 32, 44, 0, 44, 0, 44, 0, 44],
self::IDD_CODE => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 36, 48, 0, 48, 0, 48, 36, 48, 0, 48],
self::AREA_CODE=> [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 52, 0, 52, 0, 52, 40, 52, 0, 52],
self::WEATHER_STATION_CODE => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 36, 56, 0, 56, 0, 56, 0, 56],
self::WEATHER_STATION_NAME => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 60, 0, 60, 0, 60, 0, 60],
self::MCC => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 36, 64, 0, 64, 36, 64],
self::MNC => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 40, 68, 0, 68, 40, 68],
self::MOBILE_CARRIER_NAME => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 44, 72, 0, 72, 44, 72],
self::ELEVATION=> [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 44, 76, 0, 76],
self::USAGE_TYPE => [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 48, 80],
];
private static $names = [
self::COUNTRY_CODE => 'countryCode',
self::COUNTRY_NAME => 'countryName',
self::REGION_NAME => 'regionName',
self::CITY_NAME=> 'cityName',
self::LATITUDE => 'latitude',
self::LONGITUDE=> 'longitude',
self::ISP => 'isp',
self::DOMAIN_NAME => 'domainName',
self::ZIP_CODE => 'zipCode',
self::TIME_ZONE=> 'timeZone',
self::NET_SPEED=> 'netSpeed',
self::IDD_CODE => 'iddCode',
self::AREA_CODE=> 'areaCode',
self::WEATHER_STATION_CODE => 'weatherStationCode',
self::WEATHER_STATION_NAME => 'weatherStationName',
self::MCC => 'mcc',
self::MNC => 'mnc',
self::MOBILE_CARRIER_NAME => 'mobileCarrierName',
self::ELEVATION=> 'elevation',
self::USAGE_TYPE => 'usageType',
self::IP_ADDRESS => 'ipAddress',
self::IP_VERSION => 'ipVersion',
self::IP_NUMBER=> 'ipNumber',
];
private static $databases = [
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION-USAGETYPE',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN-MOBILE-USAGETYPE',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-AREACODE-ELEVATION',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN-MOBILE',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-NETSPEED-WEATHER',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-AREACODE',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-TIMEZONE-NETSPEED',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-ISP-DOMAIN',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN',
'IP-COUNTRY-REGION-CITY-ISP-DOMAIN',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP',
'IP-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE',
'IP-COUNTRY-REGION-CITY-ISP',
'IP-COUNTRY-REGION-CITY',
'IP-COUNTRY-ISP',
'IP-COUNTRY',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION-USAGETYPE',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN-MOBILE-USAGETYPE',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE-ELEVATION',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-AREACODE-ELEVATION',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER-MOBILE',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN-MOBILE',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE-WEATHER',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-NETSPEED-WEATHER',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED-AREACODE',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-AREACODE',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN-NETSPEED',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-TIMEZONE-NETSPEED',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE-ISP-DOMAIN',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-TIMEZONE',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE-ISP-DOMAIN',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ZIPCODE',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP-DOMAIN',
'IPV6-COUNTRY-REGION-CITY-ISP-DOMAIN',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE-ISP',
'IPV6-COUNTRY-REGION-CITY-LATITUDE-LONGITUDE',
'IPV6-COUNTRY-REGION-CITY-ISP',
'IPV6-COUNTRY-REGION-CITY',
'IPV6-COUNTRY-ISP',
'IPV6-COUNTRY',
];
private static $buffer = [];
private static $floatSize = null;
private static $memoryLimit = null;
private $mode;
private $resource = false;
private $date;
private $type;
private $columnWidth = [];
private $offset = [];
private $ipCount = [];
private $ipBase = [];
private $indexBaseAddr = [];
private $year;
private $month;
private $day;
private $defaultFields = self::ALL;


public function __construct($file = null, $mode = self::FILE_IO, $defaultFields = self::ALL) {
$rfile = self::findFile($file);
$size = filesize($rfile);
switch ($mode) {
case self::SHARED_MEMORY:
if (!extension_loaded('shmop')) {
throw new \Exception(__CLASS__ . ": Please make sure your PHP setup has the 'shmop' extension enabled.", self::EXCEPTION_NO_SHMOP);
}
$limit = self::getMemoryLimit();
if (false !== $limit && $size > $limit) {
throw new \Exception(__CLASS__ . ": Insufficient memory to load file '{$rfile}'.", self::EXCEPTION_NO_MEMORY);
}
$this->mode = self::SHARED_MEMORY;
$shmKey = self::getShmKey($rfile);
$this->resource = @shmop_open($shmKey, 'a', 0, 0);
if (false === $this->resource) {
$fp = fopen($rfile, 'rb');
if (false === $fp) {
throw new \Exception(__CLASS__ . ": Unable to open file '{$rfile}'.", self::EXCEPTION_FILE_OPEN_FAILED);
}
$shmId = @shmop_open($shmKey, 'n', self::SHM_PERMS, $size);
if (false === $shmId) {
throw new \Exception(__CLASS__ . ": Unable to create shared memory block '{$shmKey}'.", self::EXCEPTION_SHMOP_CREATE_FAILED);
}
$pointer = 0;
while ($pointer < $size) {
$buf = fread($fp, self::SHM_CHUNK_SIZE);
shmop_write($shmId, $buf, $pointer);
$pointer += self::SHM_CHUNK_SIZE;
}
shmop_close($shmId);
fclose($fp);
$this->resource = @shmop_open($shmKey, 'a', 0, 0);
if (false === $this->resource) {
throw new \Exception(__CLASS__ . ": Unable to access shared memory block '{$shmKey}' for reading.", self::EXCEPTION_SHMOP_READING_FAILED);
}
}
break;
case self::FILE_IO:
$this->mode = self::FILE_IO;
$this->resource = @fopen($rfile, 'rb');
if (false === $this->resource) {
throw new \Exception(__CLASS__ . ": Unable to open file '{$rfile}'.", self::EXCEPTION_FILE_OPEN_FAILED);
}
break;
case self::MEMORY_CACHE:
$this->mode = self::MEMORY_CACHE;
$this->resource = $rfile;
if (!array_key_exists($rfile, self::$buffer)) {
$limit = self::getMemoryLimit();
if (false !== $limit && $size > $limit) {
throw new \Exception(__CLASS__ . ": Insufficient memory to load file '{$rfile}'.", self::EXCEPTION_NO_MEMORY);
}
self::$buffer[$rfile] = @file_get_contents($rfile);
if (false === self::$buffer[$rfile]) {
throw new \Exception(__CLASS__ . ": Unable to open file '{$rfile}'.", self::EXCEPTION_FILE_OPEN_FAILED);
}
}
break;
default:
}
if (null === self::$floatSize) {
self::$floatSize = strlen(pack('f', M_PI));
}
$this->defaultFields = $defaultFields;
$this->type = $this->readByte(1) - 1;
$this->columnWidth[4] = $this->readByte(2) * 4;
$this->columnWidth[6] = $this->columnWidth[4] + 12;
$this->offset[4] = -4;
$this->offset[6] = 8;
$this->year = 2000 + $this->readByte(3);
$this->month= $this->readByte(4);
$this->day = $this->readByte(5);
$this->date = date('Y-m-d', strtotime("{$this->year}-{$this->month}-{$this->day}"));
$this->ipCount[4] = $this->readWord(6);
$this->ipBase[4] = $this->readWord(10);$this->ipCount[6] = $this->readWord(14);
$this->ipBase[6] = $this->readWord(18);
$this->indexBaseAddr[4] = $this->readWord(22);$this->indexBaseAddr[6] = $this->readWord(26);}


public function __destruct() {
switch ($this->mode) {
case self::FILE_IO:
if (false !== $this->resource) {
fclose($this->resource);
$this->resource = false;
}
break;
case self::SHARED_MEMORY:
if (false !== $this->resource) {
shmop_close($this->resource);
$this->resource = false;
}
break;
}
}


public static function shmTeardown($file) {
if (!extension_loaded('shmop')) {
throw new \Exception(__CLASS__ . ": Please make sure your PHP setup has the 'shmop' extension enabled.", self::EXCEPTION_NO_SHMOP);
}
$rfile = realpath($file);
if (false === $rfile) {
throw new \Exception(__CLASS__ . ": Database file '{$file}' does not seem to exist.", self::EXCEPTION_DBFILE_NOT_FOUND);
}
$shmKey = self::getShmKey($rfile);
$shmId = @shmop_open($shmKey, 'w', 0, 0);
if (false === $shmId) {
throw new \Exception(__CLASS__ . ": Unable to access shared memory block '{$shmKey}' for writing.", self::EXCEPTION_SHMOP_WRITING_FAILED);
}
shmop_delete($shmId);
shmop_close($shmId);
}


private static function getMemoryLimit() {
if (null === self::$memoryLimit) {
$limit = ini_get('memory_limit');
if ('' === (string) $limit) {
$limit = '128M';
}
$value = (int) $limit;
if ($value < 0) {
$value = false;
} else {
switch (strtoupper(substr($limit, -1))) {
case 'G': $value *= 1024;
case 'M': $value *= 1024;
case 'K': $value *= 1024;
}
}
self::$memoryLimit = $value;
}
return self::$memoryLimit;
}


private static function findFile($file = null) {
if (null !== $file) {
$rfile = realpath($file);
if (false === $rfile) {
throw new \Exception(__CLASS__ . ": Database file '{$file}' does not seem to exist.", self::EXCEPTION_DBFILE_NOT_FOUND);
}
return $rfile;
} else {
$current = realpath(dirname(__FILE__));
if (false === $current) {
throw new \Exception(__CLASS__ . ": Cannot determine current path.", self::EXCEPTION_NO_PATH);
}
foreach (self::$databases as $database) {
$rfile = realpath("{$current}/{$database}.BIN");
if (false !== $rfile) {
return $rfile;
}
}
throw new \Exception(__CLASS__ . ": No candidate database files found.", self::EXCEPTION_NO_CANDIDATES);
}
}

private static function wrap8($x) {
return $x + ($x < 0 ? 256 : 0);
}

private static function wrap32($x) {
return $x + ($x < 0 ? 4294967296 : 0);
}

private static function getShmKey($filename) {
return (int) sprintf('%u', self::wrap32(crc32(__FILE__ . ':' . $filename)));
}

private static function ipBetween($version, $ip, $low, $high) {
if (4 === $version) {
if ($low <= $ip) {
if ($ip < $high) {
return 0;
} else {
return 1;
}
} else {
return -1;
}
} else {
if (bccomp($low, $ip, 0) <= 0) {
if (bccomp($ip, $high, 0) <= -1) {
return 0;
} else {
return 1;
}
} else {
return -1;
}
}
}

private static function ipVersionAndNumber($ip) {
if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
return [4, sprintf('%u', ip2long($ip))];
} elseif (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
$result = 0;
foreach (str_split(bin2hex(inet_pton($ip)), 8) as $word) {
$result = bcadd(bcmul($result, '4294967296', 0), self::wrap32(hexdec($word)), 0);
}
return [6, $result];
} else {
return [false, false];
}
}

private static function bcBin2Dec($data) {
$parts = array(
unpack('V', substr($data, 12, 4)),
unpack('V', substr($data, 8, 4)),
unpack('V', substr($data, 4, 4)),
unpack('V', substr($data, 0, 4)),
);
foreach($parts as &$part)
if($part[1] < 0)
$part[1] += 4294967296;
$result = bcadd(bcadd(bcmul($parts[0][1], bcpow(4294967296, 3)), bcmul($parts[1][1], bcpow(4294967296, 2))), bcadd(bcmul($parts[2][1], 4294967296), $parts[3][1]));
return $result;
}

private function read($pos, $len) {
switch ($this->mode) {
case self::SHARED_MEMORY:
return shmop_read($this->resource, $pos, $len);
case self::MEMORY_CACHE:
return $data = substr(self::$buffer[$this->resource], $pos, $len);
default:
fseek($this->resource, $pos, SEEK_SET);
return fread($this->resource, $len);
}
}

private function readString($pos, $additional = 0) {
$spos = $this->readWord($pos) + $additional;
return $this->read($spos + 1, $this->readByte($spos + 1));
}

private function readFloat($pos) {
return unpack('f', $this->read($pos - 1, self::$floatSize))[1];
}

private function readQuad($pos) {
return self::bcBin2Dec($this->read($pos - 1, 16));
}

private function readWord($pos) {
return self::wrap32(unpack('V', $this->read($pos - 1, 4))[1]);
}

private function readByte($pos) {
return self::wrap8(unpack('C', $this->read($pos - 1, 1))[1]);
}

private function readCountryNameAndCode($pointer) {
if (false === $pointer) {
$countryCode = self::INVALID_IP_ADDRESS;
$countryName = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::COUNTRY_CODE][$this->type]) {
$countryCode = self::FIELD_NOT_SUPPORTED;
$countryName = self::FIELD_NOT_SUPPORTED;
} else {
$countryCode = $this->readString($pointer + self::$columns[self::COUNTRY_CODE][$this->type]);
$countryName = $this->readString($pointer + self::$columns[self::COUNTRY_NAME][$this->type], 3);
}
return [$countryName, $countryCode];
}

private function readRegionName($pointer) {
if (false === $pointer) {
$regionName = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::REGION_NAME][$this->type]) {
$regionName = self::FIELD_NOT_SUPPORTED;
} else {
$regionName = $this->readString($pointer + self::$columns[self::REGION_NAME][$this->type]);
}
return $regionName;
}

private function readCityName($pointer) {
if (false === $pointer) {
$cityName = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::CITY_NAME][$this->type]) {
$cityName = self::FIELD_NOT_SUPPORTED;
} else {
$cityName = $this->readString($pointer + self::$columns[self::CITY_NAME][$this->type]);
}
return $cityName;
}

private function readLatitudeAndLongitude($pointer) {
if (false === $pointer) {
$latitude = self::INVALID_IP_ADDRESS;
$longitude = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::LATITUDE][$this->type]) {
$latitude = self::FIELD_NOT_SUPPORTED;
$longitude = self::FIELD_NOT_SUPPORTED;
} else {
$latitude = $this->readFloat($pointer + self::$columns[self::LATITUDE][$this->type]);
$longitude = $this->readFloat($pointer + self::$columns[self::LONGITUDE][$this->type]);
}
return [$latitude, $longitude];
}

private function readIsp($pointer) {
if (false === $pointer) {
$isp = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::ISP][$this->type]) {
$isp = self::FIELD_NOT_SUPPORTED;
} else {
$isp = $this->readString($pointer + self::$columns[self::ISP][$this->type]);
}
return $isp;
}

private function readDomainName($pointer) {
if (false === $pointer) {
$domainName = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::DOMAIN_NAME][$this->type]) {
$domainName = self::FIELD_NOT_SUPPORTED;
} else {
$domainName = $this->readString($pointer + self::$columns[self::DOMAIN_NAME][$this->type]);
}
return $domainName;
}

private function readZipCode($pointer) {
if (false === $pointer) {
$zipCode = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::ZIP_CODE][$this->type]) {
$zipCode = self::FIELD_NOT_SUPPORTED;
} else {
$zipCode = $this->readString($pointer + self::$columns[self::ZIP_CODE][$this->type]);
}
return $zipCode;
}

private function readTimeZone($pointer) {
if (false === $pointer) {
$timeZone = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::TIME_ZONE][$this->type]) {
$timeZone = self::FIELD_NOT_SUPPORTED;
} else {
$timeZone = $this->readString($pointer + self::$columns[self::TIME_ZONE][$this->type]);
}
return $timeZone;
}

private function readNetSpeed($pointer) {
if (false === $pointer) {
$netSpeed = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::NET_SPEED][$this->type]) {
$netSpeed = self::FIELD_NOT_SUPPORTED;
} else {
$netSpeed = $this->readString($pointer + self::$columns[self::NET_SPEED][$this->type]);
}
return $netSpeed;
}

private function readIddAndAreaCodes($pointer) {
if (false === $pointer) {
$iddCode = self::INVALID_IP_ADDRESS;
$areaCode = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::IDD_CODE][$this->type]) {
$iddCode = self::FIELD_NOT_SUPPORTED;
$areaCode = self::FIELD_NOT_SUPPORTED;
} else {
$iddCode = $this->readString($pointer + self::$columns[self::IDD_CODE][$this->type]);
$areaCode = $this->readString($pointer + self::$columns[self::AREA_CODE][$this->type]);
}
return [$iddCode, $areaCode];
}

private function readWeatherStationNameAndCode($pointer) {
if (false === $pointer) {
$weatherStationName = self::INVALID_IP_ADDRESS;
$weatherStationCode = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::WEATHER_STATION_NAME][$this->type]) {
$weatherStationName = self::FIELD_NOT_SUPPORTED;
$weatherStationCode = self::FIELD_NOT_SUPPORTED;
} else {
$weatherStationName = $this->readString($pointer + self::$columns[self::WEATHER_STATION_NAME][$this->type]);
$weatherStationCode = $this->readString($pointer + self::$columns[self::WEATHER_STATION_CODE][$this->type]);
}
return [$weatherStationName, $weatherStationCode];
}

private function readMccMncAndMobileCarrierName($pointer) {
if (false === $pointer) {
$mcc = self::INVALID_IP_ADDRESS;
$mnc = self::INVALID_IP_ADDRESS;
$mobileCarrierName = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::MCC][$this->type]) {
$mcc = self::FIELD_NOT_SUPPORTED;
$mnc = self::FIELD_NOT_SUPPORTED;
$mobileCarrierName = self::FIELD_NOT_SUPPORTED;
} else {
$mcc = $this->readString($pointer + self::$columns[self::MCC][$this->type]);
$mnc = $this->readString($pointer + self::$columns[self::MNC][$this->type]);
$mobileCarrierName = $this->readString($pointer + self::$columns[self::MOBILE_CARRIER_NAME][$this->type]);
}
return [$mcc, $mnc, $mobileCarrierName];
}

private function readElevation($pointer) {
if (false === $pointer) {
$elevation = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::ELEVATION][$this->type]) {
$elevation = self::FIELD_NOT_SUPPORTED;
} else {
$elevation = $this->readString($pointer + self::$columns[self::ELEVATION][$this->type]);
}
return $elevation;
}

private function readUsageType($pointer) {
if (false === $pointer) {
$usageType = self::INVALID_IP_ADDRESS;
} elseif (0 === self::$columns[self::USAGE_TYPE][$this->type]) {
$usageType = self::FIELD_NOT_SUPPORTED;
} else {
$usageType = $this->readString($pointer + self::$columns[self::USAGE_TYPE][$this->type]);
}
return $usageType;
}

private function readIp($version, $pos) {
if (4 === $version) {
return self::wrap32($this->readWord($pos));
} elseif (6 === $version) {
return $this->readQuad($pos);
} else {
return false;
}
}

private function binSearch($version, $ipNumber) {
if (false === $version) {
return false;
}
$base = $this->ipBase[$version];
$offset = $this->offset[$version];
$width = $this->columnWidth[$version];
$high = $this->ipCount[$version];
$low= 0;
$indexBaseStart = $this->indexBaseAddr[$version];
if ($indexBaseStart > 0){
$indexPos = 0;
switch($version){
case 4:
$ipNum1_2 = intval($ipNumber >> 16);
$indexPos = $indexBaseStart + ($ipNum1_2 << 3);
break;
case 6:
$ipNum1 = intval(bcdiv($ipNumber, bcpow('2', '112')));
$indexPos = $indexBaseStart + ($ipNum1 << 3);
break;
default:
return false;
}
$low = $this->readWord($indexPos);
$high = $this->readWord($indexPos + 4);
}
while ($low <= $high) {
$mid = (int) ($low + (($high - $low) >> 1));
$ip_from = $this->readIp($version, $base + $width * $mid);
$ip_to = $this->readIp($version, $base + $width * ($mid + 1));
switch (self::ipBetween($version, $ipNumber, $ip_from, $ip_to)) {
case 0:
return $base + $offset + $mid * $width;
case -1:
$high = $mid - 1;
break;
case 1:
$low = $mid + 1;
break;
}
}
return false;
}

public function getDate() {
return $this->date;
}

public function getType() {
return $this->type + 1;
}

public function getFields($asNames = false) {
$result = array_keys(array_filter(self::$columns, function ($field) {
return 0 !== $field[$this->type];
}));
if ($asNames) {
$return = [];
foreach ($result as $field) {
$return[] = self::$names[$field];
}
return $return;
} else {
return $result;
}
}

public function getModuleVersion() {
return self::VERSION;
}

public function getDatabaseVersion() {
return $this->year . '.' . $this->month . '.' . $this->day;
}

public function lookup($ip, $fields = null, $asNamed = true) {
list($ipVersion, $ipNumber) = self::ipVersionAndNumber($ip);
$pointer = $this->binSearch($ipVersion, $ipNumber);
if (null === $fields) {
$fields = $this->defaultFields;
}
$ifields = (array) $fields;
if (in_array(self::ALL, $ifields)) {
$ifields[] = self::REGION_NAME;
$ifields[] = self::CITY_NAME;
$ifields[] = self::ISP;
$ifields[] = self::DOMAIN_NAME;
$ifields[] = self::ZIP_CODE;
$ifields[] = self::TIME_ZONE;
$ifields[] = self::NET_SPEED;
$ifields[] = self::ELEVATION;
$ifields[] = self::USAGE_TYPE;
$ifields[] = self::COUNTRY;
$ifields[] = self::COORDINATES;
$ifields[] = self::IDD_AREA;
$ifields[] = self::WEATHER_STATION;
$ifields[] = self::MCC_MNC_MOBILE_CARRIER_NAME;
$ifields[] = self::IP_ADDRESS;
$ifields[] = self::IP_VERSION;
$ifields[] = self::IP_NUMBER;
}
$afields = array_keys(array_flip($ifields));
rsort($afields);
$done= [
self::COUNTRY_CODE=> false,
self::COUNTRY_NAME=> false,
self::REGION_NAME => false,
self::CITY_NAME => false,
self::LATITUDE=> false,
self::LONGITUDE => false,
self::ISP => false,
self::DOMAIN_NAME => false,
self::ZIP_CODE=> false,
self::TIME_ZONE => false,
self::NET_SPEED => false,
self::IDD_CODE=> false,
self::AREA_CODE => false,
self::WEATHER_STATION_CODE=> false,
self::WEATHER_STATION_NAME=> false,
self::MCC => false,
self::MNC => false,
self::MOBILE_CARRIER_NAME => false,
self::ELEVATION => false,
self::USAGE_TYPE => false,
self::COUNTRY => false,
self::COORDINATES => false,
self::IDD_AREA=> false,
self::WEATHER_STATION => false,
self::MCC_MNC_MOBILE_CARRIER_NAME => false,
self::IP_ADDRESS => false,
self::IP_VERSION => false,
self::IP_NUMBER => false,
];
$results = [];
foreach ($afields as $afield) {
switch ($afield) {
case self::ALL: break;
case self::COUNTRY:
if (!$done[self::COUNTRY]) {
list($results[self::COUNTRY_NAME], $results[self::COUNTRY_CODE]) = $this->readCountryNameAndCode($pointer);
$done[self::COUNTRY] = true;
$done[self::COUNTRY_CODE] = true;
$done[self::COUNTRY_NAME] = true;
}
break;
case self::COORDINATES:
if (!$done[self::COORDINATES]) {
list($results[self::LATITUDE], $results[self::LONGITUDE]) = $this->readLatitudeAndLongitude($pointer);
$done[self::COORDINATES] = true;
$done[self::LATITUDE]= true;
$done[self::LONGITUDE] = true;
}
break;
case self::IDD_AREA:
if (!$done[self::IDD_AREA]) {
list($results[self::IDD_CODE], $results[self::AREA_CODE]) = $this->readIddAndAreaCodes($pointer);
$done[self::IDD_AREA] = true;
$done[self::IDD_CODE] = true;
$done[self::AREA_CODE] = true;
}
break;
case self::WEATHER_STATION:
if (!$done[self::WEATHER_STATION]) {
list($results[self::WEATHER_STATION_NAME], $results[self::WEATHER_STATION_CODE]) = $this->readWeatherStationNameAndCode($pointer);
$done[self::WEATHER_STATION] = true;
$done[self::WEATHER_STATION_NAME] = true;
$done[self::WEATHER_STATION_CODE] = true;
}
break;
case self::MCC_MNC_MOBILE_CARRIER_NAME:
if (!$done[self::MCC_MNC_MOBILE_CARRIER_NAME]) {
list($results[self::MCC], $results[self::MNC], $results[self::MOBILE_CARRIER_NAME]) = $this->readMccMncAndMobileCarrierName($pointer);
$done[self::MCC_MNC_MOBILE_CARRIER_NAME] = true;
$done[self::MCC] = true;
$done[self::MNC] = true;
$done[self::MOBILE_CARRIER_NAME] = true;
}
break;
case self::COUNTRY_CODE:
if (!$done[self::COUNTRY_CODE]) {
$results[self::COUNTRY_CODE] = $this->readCountryNameAndCode($pointer)[1];
$done[self::COUNTRY_CODE]= true;
}
break;
case self::COUNTRY_NAME:
if (!$done[self::COUNTRY_CODE]) {
$results[self::COUNTRY_CODE] = $this->readCountryNameAndCode($pointer)[0];
$done[self::COUNTRY_CODE]= true;
}
break;
case self::REGION_NAME:
if (!$done[self::REGION_NAME]) {
$results[self::REGION_NAME] = $this->readRegionName($pointer);
$done[self::REGION_NAME]= true;
}
break;
case self::CITY_NAME:
if (!$done[self::CITY_NAME]) {
$results[self::CITY_NAME] = $this->readCityName($pointer);
$done[self::CITY_NAME]= true;
}
break;
case self::LATITUDE:
if (!$done[self::LATITUDE]) {
$results[self::LATITUDE] = $this->readLatitudeAndLongitude($pointer)[0];
$done[self::LATITUDE]= true;
}
break;
case self::LONGITUDE:
if (!$done[self::LONGITUDE]) {
$results[self::LONGITUDE] = $this->readLatitudeAndLongitude($pointer)[1];
$done[self::LONGITUDE]= true;
}
break;
case self::ISP:
if (!$done[self::ISP]) {
$results[self::ISP] = $this->readIsp($pointer);
$done[self::ISP]= true;
}
break;
case self::DOMAIN_NAME:
if (!$done[self::DOMAIN_NAME]) {
$results[self::DOMAIN_NAME] = $this->readDomainName($pointer);
$done[self::DOMAIN_NAME]= true;
}
break;
case self::ZIP_CODE:
if (!$done[self::ZIP_CODE]) {
$results[self::ZIP_CODE] = $this->readZipCode($pointer);
$done[self::ZIP_CODE]= true;
}
break;
case self::TIME_ZONE:
if (!$done[self::TIME_ZONE]) {
$results[self::TIME_ZONE] = $this->readTimeZone($pointer);
$done[self::TIME_ZONE]= true;
}
break;
case self::NET_SPEED:
if (!$done[self::NET_SPEED]) {
$results[self::NET_SPEED] = $this->readNetSpeed($pointer);
$done[self::NET_SPEED]= true;
}
break;
case self::IDD_CODE:
if (!$done[self::IDD_CODE]) {
$results[self::IDD_CODE] = $this->readIddAndAreaCodes($pointer)[0];
$done[self::IDD_CODE]= true;
}
break;
case self::AREA_CODE:
if (!$done[self::AREA_CODE]) {
$results[self::AREA_CODE] = $this->readIddAndAreaCodes($pointer)[1];
$done[self::AREA_CODE]= true;
}
break;
case self::WEATHER_STATION_CODE:
if (!$done[self::WEATHER_STATION_CODE]) {
$results[self::WEATHER_STATION_CODE] = $this->readWeatherStationNameAndCode($pointer)[1];
$done[self::WEATHER_STATION_CODE]= true;
}
break;
case self::WEATHER_STATION_NAME:
if (!$done[self::WEATHER_STATION_NAME]) {
$results[self::WEATHER_STATION_NAME] = $this->readWeatherStationNameAndCode($pointer)[0];
$done[self::WEATHER_STATION_NAME]= true;
}
break;
case self::MCC:
if (!$done[self::MCC]) {
$results[self::MCC] = $this->readMccMncAndMobileCarrierName($pointer)[0];
$done[self::MCC]= true;
}
break;
case self::MNC:
if (!$done[self::MNC]) {
$results[self::MNC] = $this->readMccMncAndMobileCarrierName($pointer)[1];
$done[self::MNC]= true;
}
break;
case self::MOBILE_CARRIER_NAME:
if (!$done[self::MOBILE_CARRIER_NAME]) {
$results[self::MOBILE_CARRIER_NAME] = $this->readMccMncAndMobileCarrierName($pointer)[2];
$done[self::MOBILE_CARRIER_NAME]= true;
}
break;
case self::ELEVATION:
if (!$done[self::ELEVATION]) {
$results[self::ELEVATION] = $this->readElevation($pointer);
$done[self::ELEVATION]= true;
}
break;
case self::USAGE_TYPE:
if (!$done[self::USAGE_TYPE]) {
$results[self::USAGE_TYPE] = $this->readUsageType($pointer);
$done[self::USAGE_TYPE]= true;
}
break;
case self::IP_ADDRESS:
if (!$done[self::IP_ADDRESS]) {
$results[self::IP_ADDRESS] = $ip;
$done[self::IP_ADDRESS]= true;
}
break;
case self::IP_VERSION:
if (!$done[self::IP_VERSION]) {
$results[self::IP_VERSION] = $ipVersion;
$done[self::IP_VERSION]= true;
}
break;
case self::IP_NUMBER:
if (!$done[self::IP_NUMBER]) {
$results[self::IP_NUMBER] = $ipNumber;
$done[self::IP_NUMBER]= true;
}
break;
default:
$results[$afield] = self::FIELD_NOT_KNOWN;
}
}
if (is_array($fields) || count($results) > 1) {
if ($asNamed) {
$return = [];
foreach ($results as $key => $val) {
if (array_key_exists($key, static::$names)) {
$return[static::$names[$key]] = $val;
} else {
$return[$key] = $val;
}
}
return $return;
} else {
return $results;
}
} else {
return array_values($results)[0];
}
}
}//namespace IP2Location end


?>