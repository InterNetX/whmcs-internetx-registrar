<?php

namespace App\Providers;

use App\Providers\WhmcsModuleLogProvider as Logger;

class WhoisProxyProvider {

    private static function makeWhoisRequest($request, $log = false){
        $fp = fsockopen("whois.autodns3.de", 43, $errno, $errstr, 30);
        if (!$fp) {
            if ($log) Logger::log('Autodns_Whois', "whois.autodns3.de", "Fsockopen Error: $errstr ($errno)");
            return false;
        } else {
            fwrite($fp, $request."\r\n");
            $data = "";
            while (!feof($fp)) {
                $data .= fread($fp,32000);
            }
            fclose($fp);
        }
        return $data;
    }

    private static function parseWhoisResponse($data, $slds, $tlds){
        //parse response
        $domains = [];
        $preg = '~(?i)('.implode('|',$slds).')[.]('.implode('|',$tlds).'):~';

        foreach(explode("\r\n",$data) as $line){
            if(!preg_match($preg,$line)){
                continue;
            }
            list ($domain,$status) = explode(':',$line,2);
            if(substr(trim($status),0,7) == 'premium'){
                $premium = 1;
                list($status,$premiumClass) = explode(',',$status,2);
            }
            else{
                $premium = 0;
                $premiumClass = false;
            }
            $domains[$domain] = [
                'status' => trim($status),
                'premium' => $premium,
                'premiumClass' => $premiumClass,
            ];
        }
        return $domains;
    }

    private static function cleanUpTlds($tlds){
        // Remove leading dots from List of TLDs
        // e.g: [".com",".net",".org"] => ["com","net","org"]
        foreach ($tlds as $key => $tld) {
            $tlds[$key] = ltrim($tld, '.');
        }
        return $tlds;
    }

    public static function whois($slds = [], $tlds = [], $log = false){

        // Accept "sld" but Wrap as ["sld"]
        $slds = is_array($slds) ? $slds : [$slds];
        $tlds = is_array($tlds) ? $tlds : [$tlds];

        $tlds     = self::cleanUpTlds($tlds);
        $request  = "multi +v2 +priceclass ".implode(',', $slds)." ".implode(',', $tlds);
        $response = self::makeWhoisRequest($request, $log);
        $domains  = self::parseWhoisResponse($response, $slds, $tlds);

        if ($log) Logger::log('Autodns_Whois', print_r($request, true), print_r($response, true));
        return $domains;

    }
}
