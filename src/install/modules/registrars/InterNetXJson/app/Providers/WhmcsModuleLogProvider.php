<?php

namespace App\Providers;

use App\Helpers\Config;

class WhmcsModuleLogProvider {
    /**
     * Log module call. 1on1 Wrapper of the WHMCS logModuleCall function
     * With added check if function exists
     *
     * @param string $module The name of the module
     * @param string $action The name of the action being performed
     * @param string|array $requestString The input parameters for the API call
     * @param string|array $responseData The response data from the API call
     * @param string|array $processedData The resulting data after any post processing (eg. json decode, xml decode, etc...)
     * @param array $replaceVars An array of strings for replacement
     */

    public static function logModuleCall($module, $action, $requestString, $responseData, $processedData, $replaceVars){
        if (function_exists('logModuleCall')) {
            return logModuleCall($module, $action, $requestString, $responseData, $processedData, $replaceVars);
        }
        return null;
    }

    /**
    * Log module call without setting the module Name
    *
    * @param string $action The name of the action being performed
    * @param string|array $requestString The input parameters for the API call
    * @param string|array $responseData The response data from the API call
    * @param string|array $processedData The resulting data after any post processing (eg. json decode, xml decode, etc...)
    * @param array $replaceVars An array of strings for replacement
    */

    public static function log($action, $requestString, $responseData, $processedData = '', $replaceVars = []){
        return self::logModuleCall(Config::moduleName(), $action, $requestString, $responseData, $processedData, $replaceVars);
    }


}