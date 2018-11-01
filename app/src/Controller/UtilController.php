<?php
namespace App\Controller;

class UtilController
{
    public function clearString($content)
    {
        if (is_array($content)) {
            return filter_var_array($content, FILTER_SANITIZE_STRING);
        } else {
            return filter_var($content, FILTER_SANITIZE_STRING);
        }
    }

    public function timestampToDatetime(int $timestamp)
    {
        return date('Y-m-d H:i:s', $timestamp);
    }

    public function getGeolocation()
    {
        $geolocation = isset($_SERVER['HTTP_CF_IPCOUNTRY']) ? $_SERVER['HTTP_CF_IPCOUNTRY'] : null;

        return $geolocation;
    }
}