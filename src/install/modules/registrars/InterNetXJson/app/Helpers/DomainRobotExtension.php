<?php

namespace App\Helpers;

class DomainRobotExtension
{

    public static $tlds = array(
        "au", "aero", "cat", "hk", "it", "jobs", "pro",
        "ru", "uk", "ro", "ca", "travel", "fr", "es", "nu", "swiss"
    );

    /**
     * FUNCTION assign
     * Search tld and assign extension
     * @param type $searchTld
     * @param type $domainFiels
     * @return type
     */
    public static function assign($searchTld, $domainFiels)
    {
        if (in_array($searchTld, self::$tlds)){
            return call_user_func([self::class, $searchTld], $domainFiels);
        }
        return false;
    }

    /**
     * FUNCTION au
     * Extension  for au
     * @param array $domainFiels
     * @return array
     */
    public static function au($domainFiels)
    {

        $policy_reason = "";
        if ($domainFiels['Eligibility Reason'] == "Domain name is an Exact Match Abbreviation or Acronym of your Entity or Trading Name.")
            $policy_reason = "1";
        else $policy_reason = "2";

        return array(
            "handle" => array(
                "au_registrant_id"       => $domainFiels['Registrant ID'],
                "au_registrant_id_type"  => $domainFiels['Registrant ID Type'],
                "au_eligibility_type"    => $domainFiels['Eligibility Type'],
                "au_eligibility_name"    => $domainFiels['Eligibility Name'],
                "au_eligibility_id"      => $domainFiels['Eligibility ID'],
                "au_eligibility_id_type" => $domainFiels['Eligibility ID Type'],
                "au_policy_reason"       => $policy_reason
            ),
        );
    }

    /**
     * FUNCTION aero
     * Extension  for aero
     * @param array $domainFiels
     * @return array
     */
    public static function aero($domainFiels)
    {

        return array(
            "handle" => array(
                "aero_ens_auth_id" => $domainFiels['.aero ID Number'],
                "aero_ens_key"     => $domainFiels['.aero Password'],
            ),
        );
    }

    /**
     * FUNCTION cat
     * Extension  for cat
     * @param array $domainFiels
     * @return array
     */
    public static function cat($domainFiels)
    {

        return array(
            "handle" => array(
                "cat_ens_auth_id"  => $domainFiels['ENS Auth ID'],
                "cat_ens_key"      => $domainFiels['ENS Key'],
                "cat_intended_use" => $domainFiels['Intended Use'],
            ),
        );
    }

    /**
     * FUNCTION hk
     * Extension  for hk
     * @param array $domainFiels
     * @return array
     */
    public static function hk($domainFiels)
    {

        return array(
            "handle" => array(
                "hk_document_type"   => $domainFiels['Individual Document Type'],
                "hk_others"          => $domainFiels['Specify the Organization Type'],
                "hk_document_number" => $domainFiels['Document Number'],
                "hk_document_origin" => $domainFiels['Country Origin Country'],
                "hk_above_18"        => strtolower($domainFiels['Are you Adult']),
                "hk_industry_type"   => $domainFiels['Industry Type']
            ),
        );
    }

    /**
     * FUNCTION it
     * Extension  for it
     * @param array $domainFiels
     * @return array
     */
    public static function it($domainFiels)
    {

        return array(
            "handle" => array(
                "entityType"     => $domainFiels['IT Entity Type'],
                "number"       => $domainFiels['ID Number'],
                "vatNumber"      => $domainFiels['vatnumber'],
            ),
        );
    }

    /**
     * FUNCTION jobs
     * Extension  for jobs
     * @param array $domainFiels
     * @return array
     */
    public static function jobs($domainFiels)
    {

        return array(
            "handle" => array(
                "jobs_contact_title"  => $domainFiels['Position'],
                "jobs_website"        => $domainFiels['Website'],
                "jobs_industry_class" => $domainFiels['Industry Class'],
                "jobs_admin_type"     => $domainFiels['HR Member'],
            ),
        );
    }

    /**
     * FUNCTION pro
     * Extension  for pro
     * @param array $domainFiels
     * @return array
     */
    public static function pro($domainFiels)
    {

        return array(
            "handle" => array(
                "pro_profession"     => $domainFiels['Profession'],
                "pro_authority_name" => $domainFiels['Authority Name'],
                "pro_authority_url"  => $domainFiels['Authority URL'],
                "pro_license_number" => $domainFiels['License Number'],
            ),
        );
    }

    /**
     * FUNCTION ro
     * Extension  for ro
     * @param array $domainFiels
     * @return array
     */
    public static function ro($domainFiels)
    {

        return array(
            "handle" => array(
                "ro_person_type" => $domainFiels['RO Person Type'],
                "number"       => $domainFiels['ID Card Number'],
                "companynumber"  => $domainFiels['Company Number'],
            ),
        );
    }

    /**
     * FUNCTION ru
     * Extension  for ru
     * @param array $domainFiels
     * @return array
     */
    public static function ru($domainFiels)
    {

        return array(
            "handle" => array(
                "authority"     => $domainFiels['ID authority'],
                "dateOfIssue" => $domainFiels['Date of Issue'],
                "validTill"    => $domainFiels['Validation Data'],
                "number"         => $domainFiels['ID Card Number'],
            ),
        );
    }

    /**
     * FUNCTION uk
     * Extension  for uk
     * @param array $domainFiels
     * @return array
     */
    public static function uk($domainFiels)
    {

        return array(
            "handle" => array(
                "uk_type"       => $domainFiels['UK Type'],
                "companynumber" => $domainFiels['Company Number'],
            ),
        );
    }

    /** FUNCTION ca
     * Extension  for ca
     * @param array $domainFiels
     * @return array
     */
    public static function ca($domainFiels)
    {

        return array(
            "handle" => array(
                "cira_cpr" => $domainFiels['Legal Type'],
            ),
        );
    }

    /** FUNCTION travel
     * Extension  for travel
     * @param array $domainFiels
     * @return array
     */
    public static function travel($domainFiels)
    {

        return array(
            "handle" => array(
                "travel_uin" => $domainFiels['Travel Unique Identifying Number'],
            ),
        );
    }

    /** FUNCTION es
     * Extension  for es
     * @param array $domainFiels
     * @return array
     */
    public static function es($domainFiels)
    {

        return array(
            "handle" => array(
                "vatNumber"        => $domainFiels['vatnumber'],
                "number"         => $domainFiels['idnumber'],
            ),
        );
    }

    /** FUNCTION nu
     * Extension  for nu
     * @param array $domainFiels
     * @return array
     */
    public static function nu($domainFiels)
    {

        return array(
            "handle" => array(
                "vatNumber"        => $domainFiels['vatnumber'],
                "number"         => $domainFiels['idnumber'],
            ),
        );
    }

    /** FUNCTION fr
     * Extension  for fr
     * @param array $domainFiels
     * @return array
     */
    public static function fr($domainFiels)
    {

        return array(
            "handle" => array(
                "vatNumber"        => $domainFiels['vatnumber'],
                "number"         => $domainFiels['idnumber'],
            ),
        );
    }

    /** FUNCTION swiss
     * Extension  for swiss
     * @param array $domainFiels
     * @return array
     */
    public static function swiss($domainFiels)
    {

        return array(
            "handle" => array(
                "swiss_enterprise_id" => $domainFiels['.swiss Enterprise ID'],
                "swiss_intended_use" => $domainFiels['.swiss Intended Use'],
            ),
        );
    }
}

