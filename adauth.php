#!/usr/bin/php
<?php
define("AD_DOMAIN_NAME", "DOMAIN.COM");

// Get the name of this program
$prog = $argv[0];
$user = trim(fgets(STDIN));
$pass = trim(fgets(STDIN));

// Handle domain\user and user@domain syntax append domain name
if(preg_match("/^(.+?)([\\\\@])(.+?)$/", $user, $matches)) {
        if($matches[2] == '\\') {
                if(preg_match("/^$matches[1]/i", AD_DOMAIN_NAME)) {
                        $user = "$matches[3]@".AD_DOMAIN_NAME;
                } else {
                        $user = "$matches[3]@$matches[1]";
                }
        } else if($matches[2] == '@') {
                $user = "$matches[1]@$matches[3]";
        }
} else {
        $user = "$user@".AD_DOMAIN_NAME;
}

$adservers = dns_get_record("_ldap._tcp.dc._msdcs.".AD_DOMAIN_NAME, DNS_SRV);
foreach($adservers as $server) {
        $handle = ldap_connect($server['target']);
        if($handle) {
                $bind = @ldap_bind($handle, $user, $pass);
                if ($bind) {
                        exit(0);
                } else {
                        fwrite(STDERR, $prog . "Login failed\n");
                        # TODO: Handle password expired etc.
                        if (ldap_get_option($handle,  0x0032, $extended_error)) {
                                #echo "Error Binding to LDAP: $extended_error";
                        } else {
                                #echo "Error Binding to LDAP: No additional information is available.";
                        }
                        exit(1);
                }
                break;
        }
}
exit(1);
