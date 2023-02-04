<?php

namespace common\components;

use dosamigos\leaflet\layers\Marker;
use dosamigos\leaflet\layers\TileLayer;
use dosamigos\leaflet\LeafLet;
use dosamigos\leaflet\plugins\geocoder\GeoCoder;
use dosamigos\leaflet\plugins\geocoder\ServiceNominatim;
use dosamigos\leaflet\types\Icon;
use dosamigos\leaflet\types\LatLng;


/**
 * Class MainFunctions
 */
class MainFunctions
{
    /**
     * return generated UUID
     * @return string generated UUID
     * @throws \Exception
     */
    static function GUID()
    {
        if (function_exists('com_create_guid') === true) {
            return trim(com_create_guid(), '{}');
        }
        return sprintf('%04X%04X-%04X-%04X-%04X-%04X%04X%04X',
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(16384, 20479),
            random_int(32768, 49151),
            random_int(0, 65535),
            random_int(0, 65535),
            random_int(0, 65535));
    }

    /**
     * Sort array by param
     * @param $array
     * @param $cols
     * @return array
     */
    public static function array_msort($array, $cols)
    {
        $colarr = array();
        foreach ($cols as $col => $order) {
            $colarr[$col] = array();
            foreach ($array as $k => $row) {
                $colarr[$col]['_' . $k] = strtolower($row[$col]);
            }
        }
        $eval = 'array_multisort(';
        foreach ($cols as $col => $order) {
            $eval .= '$colarr[\'' . $col . '\'],' . $order . ',';
        }
        $eval = substr($eval, 0, -1) . ');';
        eval($eval);
        $ret = array();
        foreach ($colarr as $col => $arr) {
            foreach ($arr as $k => $v) {
                $k = substr($k, 1);
                if (!isset($ret[$k])) $ret[$k] = $array[$k];
                $ret[$k][$col] = $array[$k][$col];
            }
        }
        return $ret;
    }

    /**
     * @param $str
     */
    static function logs($str)
    {
        $handle = fopen("1.txt", "r+");
        fwrite($handle, $str);
        fclose($handle);
    }

    /**
     * Возвращает  случайный цвет в hex формате.
     *
     * @return string Цвет в hex формате.
     * @throws \Exception
     */
    public static function random_color()
    {
        return MainFunctions::random_color_part() . MainFunctions::random_color_part() . MainFunctions::random_color_part();
    }

    /**
     * @return string
     * @throws \Exception
     */
    static function random_color_part()
    {
        return str_pad(dechex(random_int(0, 255)), 2, '0', STR_PAD_LEFT);
    }


    public static function getLeaflet($clientOptions, $clientEventsShow, $latitude, $longitude)
    {
        $nominatim = new ServiceNominatim();

        $geoCoderPlugin = new GeoCoder([
            'service' => $nominatim,
            'clientOptions' => [
                'showMarker' => false,
            ]
        ]);

        $center = new LatLng(['lat' => $latitude, 'lng' => $longitude]);
        $icon = new Icon(['iconUrl' => '/images/marker-icon.png', 'shadowUrl' => '/images/marker-shadow.png']);
        $marker = new Marker([
            'latLng' => $center,
            'name' => 'geoMarker',
            'clientOptions' => [
                'draggable' => true,
                'icon' => $icon,
            ],
            'clientEvents' => $clientOptions
        ]);
// The Tile Layer (very important)
        $tileLayer = new TileLayer([
            'urlTemplate' => 'http://api.mapbox.com/styles/v1/mapbox/streets-v12/tiles/{z}/{x}/{y}?access_token=pk.eyJ1IjoiYXBwcmltZWdtYmgiLCJhIjoiY2tyeXFzOTlmMDY4azJvcGpnNzRremFnMiJ9.kfgoOguPkbrglTqmS8cjoA',
            'clientOptions' => [
                'attribution' => 'Tiles &copy; <a href="http://www.osm.org/copyright" target="_blank">OpenStreetMap contributors</a> />',
                'subdomains' => '1234',
                'type' => 'osm',
                'maxZoom' => 18,
                's' => 'a',
                'ext' => 'png',

            ]
        ]);

// now our component and we are going to configure it

        $leafLet = new LeafLet([
            'name' => 'geoMap',
            'center' => $center,
            'tileLayer' => $tileLayer,
            'clientEvents' => $clientEventsShow,
        ]);
// Different layers can be added to our map using the `addLayer` function.
        $leafLet->addLayer($marker);      // add the marker
//    $leafLet->addLayer($tileLayer);  // add the tile layer

// install the plugin
        $leafLet->installPlugin($geoCoderPlugin);
        return $leafLet;
    }
}
