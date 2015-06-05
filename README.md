PHP SDK for CALLR API
============

JSON-RPC 2.0 PHP class, to use with CALLR API.

* **API documentation**: http://thecallr.com/docs/
* **Product page**: http://thecallr.com/en/products/api-json-voice-sms/
* **JSON-RPC 2.0 Specification**: http://www.jsonrpc.org/specification

* Easy to use Client class, built for **PHP 5.4+**
* Can be used for both the [API][docs-api] and [Real-time][docs-realtime] calls
* Requires: `php5-curl`

## Composer

You should use Composer (https://getcomposer.org/) to manage your PHP dependencies.
If you do not have a `composer.json` file yet, create one at the root of your project, download Composer, and launch `composer update`.

The `composer.json` file should look like this:
```json
{
  "require": {
    "callr/sdk-php": "dev-master"
  }
}
```

Add all the libaries you need in `composer.json`. Do not forget to run `composer update` each time you edit the file.

Then you just need to include one file in your code:
```php
<?php

require 'vendor/autoload.php'
```

## Usage

**Init**
```php
$api = new \CALLR\API\Client;
$api->setAuthCredentials('username', 'password');
```

### Sending SMS

#### Without options

```php
$from = 'CALLR';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$result = $api->call('sms.send', [$from, $to, $text, null]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

#### Personalized sender

> Your sender must have been authorized and respect the [sms_sender](http://thecallr.com/docs/formats/#sms_sender) format

```php
$from = 'Your Brand';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$result = $api->call('sms.send', [$from, $to, $text, null]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

#### If you want to receive replies, do not set a sender - we will automatically use a shortcode

```php
$from = '';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$result = $api->call('sms.send', [$from, $to, $text, null]);
```

*Method*
- [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

#### Force GSM encoding

```php
$from = '';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$options = new stdClass;
$options->force_encoding = 'GSM';

$result = $api->call('sms.send', [$from, $to, $text, $options]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

*Objects*
* [SMS.Options](http://thecallr.com/docs/objects/#SMS.Options)

#### Long SMS (availability depends on carrier)

```php
$from = 'CALLR';
$to   = '+33123456789';
$text = 'Some super mega ultra long text to test message longer than 160 characters ',
        'Some super mega ultra long text to test message longer than 160 characters ',
        'Some super mega ultra long text to test message longer than 160 characters';

$result = $api->call('sms.send', [$from, $to, $text, null]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

#### Specify your SMS nature (alerting or marketing)

```php
$from = 'CALLR';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$options = new stdClass;
$options->nature = 'ALERTING';

$result = $api->call('sms.send', [$from, $to, $text, $$options]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

*Objects*
* [SMS.Options](http://thecallr.com/docs/objects/#SMS.Options)

#### Custom data

```php
$from = 'CALLR';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$options = new stdClass;
$options->user_data = '42';

$result = $api->call('sms.send', [$from, $to, $text, $$options]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

*Objects*
* [SMS.Options](http://thecallr.com/docs/objects/#SMS.Options)


#### Delivery Notification - set URL to receive notifications

```php
$from = 'CALLR';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$options = new stdClass;
$options->push_dlr_enabled = true;
$options->push_dlr_url = 'http://yourdomain.com/push_delivery_path';
// $options->push_dlr_url_auth = 'login:password'; // needed if you use Basic HTTP Authentication

$result = $api->call('sms.send', [$from, $to, $text, $$options]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

*Objects*
* [SMS.Options](http://thecallr.com/docs/objects/#SMS.Options)


### Inbound SMS - set URL to receive inbound messages (MO) and replies

> **Do not set a sender if you want to receive replies** - we will automatically use a shortcode.

```php
$from = '';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$options = new stdClass;
$options->push_mo_enabled = true;
$options->push_mo_url = 'http://yourdomain.com/push_delivery_path';
// $options->push_mo_url_auth = 'login:password'; // needed if you use Basic HTTP Authentication

$result = $api->call('sms.send', [$from, $to, $text, $$options]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

*Objects*
* [SMS.Options](http://thecallr.com/docs/objects/#SMS.Options)


### Get an SMS
```php
$result = $api->call('sms.get', ['SMSHASH']);
```

*Method*
* [sms.get](http://thecallr.com/docs/api/services/sms/#sms.get)

*Objects*
* [SMS](http://thecallr.com/docs/objects/#SMS)

### SMS Global Settings

#### Get settings
```php
$result = $api->call('sms.get_settings');
```

*Method*
* [sms.get_settings](http://thecallr.com/docs/api/services/sms/#sms.get_settings)

*Objects*
* [SMS.settings](http://thecallr.com/docs/objects/#SMS.Settings)


#### Set settings

> Add options that you want to change in the object

```php
$settings = new stdClass;
$settings->push_dlr_enabled = true;
$settings->push_dlr_url = 'http://yourdomain.com/push_delivery_path';
$settings->push_mo_enabled = true;
$settings->push_mo_url = 'http://yourdomain.com/mo_delivery_path';

$result = $api->call('sms.set_settings', [$settings]);
```

> Returns the updated settings.

*Method*
* [sms.set_settings](http://thecallr.com/docs/api/services/sms/#sms.set_settings)

*Objects*
* [SMS.settings](http://thecallr.com/docs/objects/#SMS.Settings)

********************************************************************************

### REALTIME

#### Create a REALTIME app with a callback URL

App name [format](http://thecallr.com/docs/formats/#app_name)
```php
$options = new stdClass;
$options->url = 'http://yourdomain.com/realtime_callback_url';

$result = $api->call('apps.create', ['REALTIME10', 'Your app name', $options]);
```

*Method*
* [apps.create](http://thecallr.com/docs/api/services/apps/#apps.create)

*Objects*
* [REALTIME10](http://thecallr.com/docs/objects/#REALTIME10)
* [App](http://thecallr.com/docs/objects/#App)

#### Start a REALTIME outbound call

```php
$target = new stdClass;
$target->number = '+33132456789';
$target->timeout = 30;

$callOptions = new stdClass;
$callOptions->cdr_field = '42';
$callOptions->cli = 'BLOCKED';

$result = $api->cal('dialr/call.realtime', ['appHash', $target, $callOptions]);
```

*Method*
* [dialr/call.realtime](http://thecallr.com/docs/api/services/dialr/call/#dialr/call.realtime)

*Objects*
* [Target](http://thecallr.com/docs/objects/#Target)
* [REALTIME10.Call.Options](http://thecallr.com/docs/objects/#REALTIME10.Call.Options)

********************************************************************************

### DIDs

#### List available countries with DID availability
```php
$result = $api->call('did/areacode.countries');
```

*Method*
* [did/areacode.countries](http://thecallr.com/docs/api/services/did/areacode/#did/areacode.countries)

*Objects*
* [DID.Country](http://thecallr.com/docs/objects/#DID.Country)

#### Get area codes available for a specific country and DID type

```php
$result = $api->call('did/areacode.get_list', ['US', null]);
```

*Method*
* [did/areacode.get_list](http://thecallr.com/docs/api/services/did/areacode/#did/areacode.get_list)

*Objects*
* [DID.AreaCode](http://thecallr.com/docs/objects/#DID.AreaCode)

#### Get DID types available for a specific country
```php
$result = $api->call('did/areacode.types', ['US']);
```

*Method*
* [did/areacode.types](http://thecallr.com/docs/api/services/did/areacode/#did/areacode.types)

*Objects*
* [DID.Type](http://thecallr.com/docs/objects/#DID.Type)

#### Buy a DID (after a reserve)

```php
$result = $api->call('did/store.buy_order', ['OrderToken']);
```

*Method*
* [did/store.buy_order](http://thecallr.com/docs/api/services/did/store/#did/store.buy_order)

*Objects*
* [DID.Store.BuyStatus](http://thecallr.com/docs/objects/#DID.Store.BuyStatus)

#### Cancel your order (after a reserve)

```php
$result = $api->call('did/store.cancel_order', ['OrderToken']);
```

*Method*
* [did/store.cancel_order](http://thecallr.com/docs/api/services/did/store/#did/store.cancel_order)

#### Cancel a DID subscription

```php
$result = $api->call('did/store.cancel_subscription', ['DID ID']);
```

*Method*
* [did/store.cancel_subscription](http://thecallr.com/docs/api/services/did/store/#did/store.cancel_subscription)

#### View your store quota status

```php
$result = $api->call('did/store.get_quota_status');
```

*Method*
* [did/store.get_quota_status](http://thecallr.com/docs/api/services/did/store/#did/store.get_quota_status)

*Objects*
* [DID.Store.QuotaStatus](http://thecallr.com/docs/objects/#DID.Store.QuotaStatus)

#### Get a quote without reserving a DID

```php
$result = $api->call('did/store.get_quote', [0, 'GOLD', 1]);
```

*Method*
* [did/store.get_quote](http://thecallr.com/docs/api/services/did/store/#did/store.get_quote)

*Objects/
* [DID.Store.Quote](http://thecallr.com/docs/objects/#DID.Store.Quote)

#### Reserve a DID

```php
$result = $api->call('did/store.reserve', [0, 'GOLD', 1, 'RANDOM']);
```

*Method*
* [did/store.reserve](http://thecallr.com/docs/api/services/did/store/#did/store.reserve)

*Objects*
* [DID.Store.Reservation](http://thecallr.com/docs/objects/#DID.Store.Reservation)

#### View your order

```php
$result = $api->call('did/store.view_order', ['OrderToken']);
```

*Method*
* [did/store.buy_order](http://thecallr.com/docs/api/services/did/store/#did/store.view_order)

*Objects*
* [DID.Store.Reservation](http://thecallr.com/docs/objects/#DID.Store.Reservation)

********************************************************************************

### Conferencing

#### Create a conference room

```php
$params = new stdClass;
$params->open = true;

$access = [];

$result = $api->call('conference/10.create_room', ['room name', $params, $access]);
```

*Method*
* [conference/10.create_room](http://thecallr.com/docs/api/services/conference/10/#conference/10.create_room)

*Objects*
* [CONFERENCE10](http://thecallr.com/docs/objects/#CONFERENCE10)
* [CONFERENCE10.Room.Access](http://thecallr.com/docs/objects/#CONFERENCE10.Room.Access)

#### Assign a DID to a room

```php
$result = $api->call('conference/10.assign_did', ['Room ID', 'DID ID']);
```

*Method*
* [conference/10.assign_did](http://thecallr.com/docs/api/services/conference/10/#conference/10.assign_did)

#### Create a PIN protected conference room

```php
$params = new stdClass;
$params->open = true;

$access = [
    (object)['pin' => '1234', 'level' => 'GUEST'],
    (object)['pin' => '4321', 'level' => 'ADMIN', 'phone_number' => '+33123456789']
];

$result = $api->call('conference/10.create_room', ['room name', $params, $access]);
```

*Method*
* [conference/10.create_room](http://thecallr.com/docs/api/services/conference/10/#conference/10.create_room)

*Objects*
* [CONFERENCE10](http://thecallr.com/docs/objects/#CONFERENCE10)
* [CONFERENCE10.Room.Access](http://thecallr.com/docs/objects/#CONFERENCE10.Room.Access)

#### Call a room access

```php
$result = $api->call('conference/10.call_room_access', ['Room Access ID', 'BLOCKED', true]);
```

*Method*
* [conference/10.call_room_access](http://thecallr.com/docs/api/services/conference/10/#conference/10.call_room_access)

********************************************************************************

### Media

#### List your medias

```php
$result = $api->call('media/library.get_list', [null]);
```

*Method*
* [media/library.get_list](http://thecallr.com/docs/api/services/media/library/#media/library.get_list)

#### Create an empty media

```php
$result = $api->call('media/library.create', ['name']);
```

*Method*
* [media/library.create](http://thecallr.com/docs/api/services/media/library/#media/library.create)

#### Upload a media

```php
$media_id = 0;

$result = $api->call('media/library.set_content', [$media_id, 'text', 'base64_audio_data']);
```

*Method*
* [media/library.set_content](http://thecallr.com/docs/api/services/media/library/#media/library.set_content)

#### Use Text-to-Speech

```php
$media_id = 0;

$result = $api->call('media/tts.set_content', [$media_id, 'Hello world!', 'TTS-EN-GB_SERENA', null]);
```

*Method*
* [media/tts.set_content](http://thecallr.com/docs/api/services/media/tts/#media/tts.set_content)

********************************************************************************

### CDR

#### Get inbound or outbound CDRs
```php
$from = 'YYYY-MM-DD HH:MM:SS';
$to = 'YYYY-MM-DD HH:MM:SS';

$result = $api->call('cdr.get', ['OUT', $from, $to, null, null]);
```

*Method*
* [cdr.get](http://thecallr.com/docs/api/services/cdr/#cdr.get)

*Objects*
* [CDR.In](http://thecallr.com/docs/objects/#CDR.In)
* [CDR.Out](http://thecallr.com/docs/objects/#CDR.Out)
