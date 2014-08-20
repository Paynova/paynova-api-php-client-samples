#Paynova API PHP Client Sample Site

##Overview
This is an simple sample site that showcases the [Paynova REST API][] services.
[Paynova REST API]: http://docs.paynova.com/display/API/Paynova+API+Home


PHP version >= 5.3.0 required.

The following PHP extensions are required:

    curl


##Pre-requisites
* PHP version >= 5.3.0 required.
* curl

#Configure
* paynova-samples.properties with merchant credentials received from Paynova support. 
* make src/resources/callback-store-file.txt writable by the webserver
* edit src/index.php and specify where [Paynova API PHP Client][] can be found
[Paynova API PHP Client]: https://github.com/Paynova/paynova-api-php-client

#Using composer

##Build
```
composer install
```

##Run
Point your webbrowser to src/index.php


##References
The app uses the Paynova PHP API client to communicate with the Paynova REST API server, see [Paynova API PHP Client][]
