# PHP SDK Installation

## Table of contents
* [Requirements](#requirements)
* [With php-composer](#with-php-composer)
* [Without php-composer](#with-php-composer)
* [Further help](#further-help)

- - -

## Requirements
The installation and usage of the php-sdk requires the following: 
* PHP 5.4+
* php5-curl

- - -

## With php-composer
php-composer ([https://getcomposer.org/download/](https://getcomposer.org/download/)) is recommended for use with the sdk and for managing your project dependencies.  
The download page contains instructions and necessary files for installation on Windows and other platforms.  
* if not being installed as root/super user, make sure to use the switch **--install-dir=**  


1. After downloading and installing composer, make sure you have a `composer.json` file located in the document root of your project, it should contain as a minimum the following:  
    ```json
    {
      "require": {
        "callr/sdk-php": "dev-master"
      }
    }
    ```

2. As an alternative, to automatically create the composer.json and install the sdk run `composer require callr/sdk-php:dev-master`

3. In your project source files, be sure to require the file `autoload.php`
    ```php
    <?php
        require 'vendor/autoload.php';
    ```
4. Run `composer update`, which will download the sdk either via git ( if found in the environment ), or a zip and install it into the *vendor* directory. 
    ```bash
    $ composer update
    Loading composer repositories with package information
    Updating dependencies (including require-dev)
    - Installing callr/sdk-php (dev-master 09a2e40)
    Loading from cache

    Writing lock file
    Generating autoload files
    ```
- - -

## Without php-composer
If you wish to use the sdk without the dependency management of php-composer it is possible with the following steps

1. Download the sdk from the CALLR [php-sdk github](https://github.com/THECALLR/sdk-php/archive/master.zip)

2. Unzip the archive and move the `src` directory into your project structure

3. Require each object source file being used, typically for making all api calls it will be the following: 
    ```php
    // require source objects
    require '../src/CALLR/Api/Client.php';
    require '../src/CALLR/Api/Request.php';
    require '../src/CALLR/Api/Response.php';

    // get api client object 
    $api = new \CALLR\API\Client;

    // set authentication credentials
    $api->setAuthCredentials($login, $password);
    ...
    ```

4. For creating realtime application flows, the libraries needed are the following:
    ```
    // require source objects
    require '../src/CALLR/Realtime/Server.php';
    require '../src/CALLR/Realtime/Request.php';
    require '../src/CALLR/Realtime/Response.php';
    require '../src/CALLR/Realtime/CallFlow.php';
    require '../src/CALLR/Realtime/Command.php';
    require '../src/CALLR/Realtime/Command/Params.php';
    require '../src/CALLR/Realtime/Command/ConferenceParams.php';

    // get callflow object
    $flow = new CallFlow;
    ...
    ``` 

- - -

## Further help
* You will find API documentation and snippets here at [http://thecallr.com/docs/](http://thecallr.com/docs/)
* Or in our github repository [https://github.com/THECALLR/](https://github.com/THECALLR/)
* Php sdk github here ([https://github.com/THECALLR/sdk-php](https://github.com/THECALLR/sdk-php))
* Php examples here ([https://github.com/THECALLR/examples-php](https://github.com/THECALLR/examples-php))

If you have any further questions or require assistance with these examples, please contact CALLR Support
* support@callr.com
* FR: +33 (0)1 84 14 00 30

---
