phpadauth
=========

PHP code to authentication against Active Directory for use with apache mod-auth-external

DefineExternalAuth adauth pipe /usr/local/bin/adauth.php

<Location /protected>
    Satisfy Any
    Require valid-user

    # htpasswd authentication
    AuthType Basic
    AuthName "Restricted location"
    AuthUserFile /srv/htpasswd.conf

    # ad authentication
    AuthExternal adauth

    # Enable both providers
    AuthBasicProvider file external
</Location>