<?php namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class Config extends Model{
    private static $device_classes = array("EV_CHARGER" => '1',
	"MILK_ATM" => '2',
	"IRRIGATION_CONTROLLER" => '3', 
	"MOTOR_CONTROLLER" => '4',
	"EV_CHARGER" => '1',
	);
	private static $connector_types = array("EV_CHARGER" => '1',
	"CCS" => '1',
	"NORMAL" => '2', 
	"CCS2" => '3',
	);
private static $device_types = array("EV_CHARGER" => '1',
	"CCS" => '1',
	"NORMAL" => '2', 
	"CCS2" => '3',
	);
private static $tariff_types = array("TIME_BASED" => '1',
	"UNIT_BASED" => '2',
	);
	private static $tax_types = array("VAT" => '1',
	"GST" => '2',
	);
private static $roles = array(
	"Customer" => '1',
	"Partner Admin" => '2',
	"Partner Tech" => '3',
	"Partner Accountant" => '4',
	"Super Admin" => '5',
	"Super Tech" => '6',
	"Super Accountant" => '7',
	);
    public static function getDeviceClass($index = false) {
        return $index !== false ? json_encode(self::$device_classes[$index]) : json_encode(self::$device_classes);
    }
	public static function getConnectorType($index = false) {
        return $index !== false ? json_encode(self::$connector_types[$index]) : json_encode(self::$connector_types);
    }
	public static function getDeviceType($index = false) {
        return $index !== false ? json_encode(self::$device_types[$index]) : json_encode(self::$device_types);
    }
	public static function getTariffType($index = false) {
        return $index !== false ? json_encode(self::$tariff_types[$index]) : json_encode(self::$tariff_types);
    }
	public static function getTaxType($index = false) {
        return $index !== false ? json_encode(self::$tax_types[$index]) : json_encode(self::$tax_types);
    }
	public static function getUserType($index = false) {
        return $index !== false ? json_encode(self::$roles[$index]) : json_encode(self::$roles);
    }
}