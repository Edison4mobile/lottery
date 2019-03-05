<?php
/**
 * Created by PhpStorm.
 * User: Matija
 * Date: 08 Jun 18
 * Time: 21:12
 */

namespace App\Utility;


class BitcoinConvert
{
    const API_URL = "https://blockchain.info/ticker";
    const EUR = "EUR";
    const BTC_TO_SATOSHI = 100000000;

    public static function btcToEur($amount)
    {
        $rates = file_get_contents(self::API_URL);
        $euroRate = json_decode($rates) -> EUR;
        $btcEur = $euroRate -> last;

        return $amount * $btcEur;
    }

    public static function eurToBtc($amount)
    {
        $rates = file_get_contents(self::API_URL);
        $euroRate = json_decode($rates) -> EUR;
        $btcEur = $euroRate -> last;

        return $amount / $btcEur;
    }

    public static function satoshiToBtc($satoshis){
        return $satoshis / self::BTC_TO_SATOSHI;
    }

    public static function btcToSatoshi($btc){
        return $btc * self::BTC_TO_SATOSHI;
    }

    static public function satoshiToEur($satoshi)
    {
        return self::btcToEur(self::satoshiToBtc($satoshi));
    }

    static public function eurToSatoshi($eur)
    {
        return self::btcToSatoshi(self::eurToBtc($eur));
    }


}