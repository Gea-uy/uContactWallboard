<?php

authenticate("mgea", "ElMono2022\"");

function authenticate($user, $password)
{
    if (empty($user) || empty($password)) return false;

    // Active Directory server
    $ldap_host = "201.217.144.22";
    // $ldap_host = "srv-dc.isbel.com.uy";
    // 201.217.144.22

    // Active Directory DN
    // $ldap_dn = "OU=SSPP,DC=isbel,DC=com,DC=uy";
    $ldap_dn = "OU=Ingenieria de Ventas,DC=isbel,DC=com,DC=uy";

    // Active Directory user group
    $ldap_user_group = "SoporteT1";

    $ldap_user_group_lectura = "Usuarios AX";

    // Active Directory manager group
    // $ldap_manager_group = "SoporteT1";
    $ldap_manager_group = "Ingenieria de Ventas";



    // Domain, for purposes of constructing $user
    $ldap_usr_dom = '@isbel.com.uy';

    // connect to active directory
    $ldap = ldap_connect($ldap_host, 10389);

    // verify user and password
    if ($bind = @ldap_bind($ldap, $user . $ldap_usr_dom, $password)) {
        // valid
        // check presence in groups
        $filter = "(sAMAccountName=" . $user . ")";
        $attr = array("memberof", "displayname");
        $result = ldap_search($ldap, $ldap_dn, $filter, $attr) or exit("Unable to search LDAP server");
        $entries = ldap_get_entries($ldap, $result);
        $displayname = $entries[0]['displayname'][0];
        ldap_unbind($ldap);

        // check groups
        foreach ($entries[0]['memberof'] as $grps) {
            // is manager, break loop
            if (strpos($grps, $ldap_manager_group)) {
                $access = 2;
                echo "$access = 2";
                break;
            }

            // is user
            if (strpos($grps, $ldap_user_group)) {
                $access = 1;
                echo "$access = 2";
                break;
            }



            if (strpos($grps, $ldap_user_group_lectura)) {
                $access = 3;
            }
        }




        if ($access != 0) {
            // establish session variables
            $_SESSION['displayname'] = $displayname;
            echo "true";
            return true;
        } else {
            // user has no rights
            echo "false";
            return false;

        }
    } else {
        // invalid name or password
        echo "invalid name or password";
        return false;
    }
}
