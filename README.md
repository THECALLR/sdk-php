PHP SDK for CALLR API
============

JSON-RPC 2.0 PHP class, to use with CALLR API.

* **API documentation**: http://thecallr.com/docs/
* **Product page**: http://thecallr.com/en/products/api-json-voice-sms/
* **JSON-RPC 2.0 Specification**: http://www.jsonrpc.org/specification

* Easy to use Client class, built for **PHP 5.4+**
* Can be used for both the [API][docs-api] and [Real-time][docs-realtime] calls
* Requires: `php5-curl`

[docs-api]: http://thecallr.com/docs/
[docs-realtime]: http://thecallr.com/docs/real-time/

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

Add all the libraries you need in `composer.json`. Do not forget to run `composer update` each time you edit the file.

Then you just need to include one file in your code:
```php
<?php

require 'vendor/autoload.php';
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
$from = 'SMS';
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

#### If you want to receive replies, do not set a sender - we will automatically use an SMS number

```php
$from = '';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$result = $api->call('sms.send', [$from, $to, $text, null]);
```

*Method*
- [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

#### Force GSM encoding

The default behaviour is to send your SMS with GSM 7-bit encoding. However, if your text contains a character that is not in the GSM 7-bit charset (Basic Character Set), we will send it as 16-bit UCS-2 (UNICODE) - using 2 bytes per character.

You can however force the encoding to be used at any time, using the **force_encoding** property.

If you force a GSM encoding, we will try to convert non-GSM characters to GSM ones. « becomes ", € becomes e, etc. The full mapping is available when calling the method [sms.get_gsm_charset_mapping](http://thecallr.com/docs/api/services/sms/#sms.get_gsm_charset_mapping).


Please note that whatever the encoding forced or used, you always send your text as a JSON string to our API, without any special processing. The charset is applied in our platform before sending to the carriers.


```php
$from = '';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$options = new stdClass;
$options->force_encoding = 'GSM'; // or 'UNICODE'

$result = $api->call('sms.send', [$from, $to, $text, $options]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

*Objects*
* [SMS.Options](http://thecallr.com/docs/objects/#SMS.Options)

#### Long SMS (availability depends on carrier)

We automatically handle concatenated SMS. The number of SMS parts billed will be set on the **parts** property of the [SMS](http://thecallr.com/docs/objects/#SMS) object. The object can be sent to you using [Webhooks](http://thecallr.com/docs/webhooks/).


**If your SMS is GSM 7-bit encoded:**
- If it's equals or less than 160 characters, it counts as 1 SMS.
- If it's more than 160 characters, the split is done every 153 characters.

**If your SMS is UNICODE encoded:**
- If it's equals or less than 70 characeters, it counts as 1 SMS.
- If it's more than 70 characters, the split is done every 67 characters.

```php
$from = 'SMS';
$to   = '+33123456789';
$text = 'Some super mega ultra long text to test message longer than 160 characters '.
        'Some super mega ultra long text to test message longer than 160 characters '.
        'Some super mega ultra long text to test message longer than 160 characters';

$result = $api->call('sms.send', [$from, $to, $text, null]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

#### Specify your SMS nature (alerting or marketing)

```php
$from = 'SMS';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$options = new stdClass;
$options->nature = 'ALERTING'; // or 'MARKETING'

$result = $api->call('sms.send', [$from, $to, $text, $options]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

*Objects*
* [SMS.Options](http://thecallr.com/docs/objects/#SMS.Options)

#### Custom data

```php
$from = 'SMS';
$to   = '+33123456789';
$text = 'Hello, SMS world!';

$options = new stdClass;
$options->user_data = '42';

$result = $api->call('sms.send', [$from, $to, $text, $options]);
```

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)

*Objects*
* [SMS.Options](http://thecallr.com/docs/objects/#SMS.Options)


#### Delivery Notification - set URL to receive notifications

To receive delivery notifications (DLR), you have to subscribe to the webhook **sms.mt.status_update** (see [below](#webhooks)).

*Method*
* [sms.send](http://thecallr.com/docs/api/services/sms/#sms.send)
* [webhooks.subscribe](http://thecallr.com/docs/api/services/webhooks/#webhooks.subscribe)


### Inbound SMS - set URL to receive inbound messages (MO) and replies

> **Do not set a sender if you want to receive replies** - we will automatically use an SMS number.

To receive inbound messages (MO), you have to subscribe to the webhook **sms.mo** (see [below](#webhooks)).

*Method*
* [webhooks.subscribe](http://thecallr.com/docs/api/services/webhooks/#webhooks.subscribe)


### Get an SMS
```php
$result = $api->call('sms.get', ['SMSHASH']);
```

*Method*
* [sms.get](http://thecallr.com/docs/api/services/sms/#sms.get)

*Objects*
* [SMS](http://thecallr.com/docs/objects/#SMS)

********************************************************************************

### Webhooks

> **See our online documentation**: http://thecallr.com/docs/webhooks/

#### Subscribe to webhooks

```php
$type = 'sms.mt.status_update';
$endpoint = 'http://yourdomain.com/webhook_url';

$result = $api->call('webhooks.subscribe', [ $type, $endpoint, null ]);
```

*Method*
* [webhooks.subscribe](http://thecallr.com/docs/api/services/webhooks/#webhooks.subscribe)

*Objects*
* [Webhook](http://thecallr.com/docs/objects/#Webhook)

#### List available webhooks

```php
$result = $api->call('webhooks.get_event_types');
```

*Method*
* [webhooks.get_event_types](http://thecallr.com/docs/api/services/webhooks/#webhooks.get_event_types)


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

$result = $api->call('calls.realtime', ['appHash', $target, $callOptions]);
```

*Method*
* [calls.realtime](http://thecallr.com/docs/api/services/calls/#calls.realtime)

*Objects*
* [Target](http://thecallr.com/docs/objects/#Target)
* [REALTIME10.Call.Options](http://thecallr.com/docs/objects/#REALTIME10.Call.Options)

#### Inbound Calls - Assign a phone number to a REALTIME app

```php
$result = $api->call('apps.assign_did', ['appHash', 'DID ID']);
```

*Method*
* [apps.assign_did](http://thecallr.com/docs/api/services/apps/#apps.assign_did)

*Objects*
* [App](http://thecallr.com/docs/objects/#App)
* [DID](http://thecallr.com/docs/objects/#DID)

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
$result = $api->call('did/store.cancel_subscription', ['DID_ID']);
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
$audio_data = base64_encode(file_get_contents('/tmp/audio.mp3'));
$text_content = 'Hi, this is the optional "text" content of the audio file.';

$result = $api->call('media/library.set_content', [$media_id, $text_content, $audio_data]);
```

*Method*
* [media/library.set_content](http://thecallr.com/docs/api/services/media/library/#media/library.set_content)

#### Use Text-to-Speech

```php
$media_id = 0;

$result = $api->call('media/tts.set_content', [$media_id, 'Hello world!', 'TTS_EN-GB_SERENA', null]);
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

********************************************************************************


### Broadcast messages to a target

```php
$target = new stdClass;
$target->number = '+33123456789';
$target->timeout = 30;

$messages = [131, 132, 'TTS|TTS_EN-GB_SERENA|Hello world! how are you ? I hope you enjoy this call. good bye.'];

$options = new stdClass;
$options->cdr_field = 'userData';
$options->cli = 'BLOCKED';
$options->loop = 2;

$result = $api->call('calls.broadcast_1', [$target, $messages, $options]);
```

#### Without options

```php
$target = new stdClass;
$target->number = '+33123456789';
$target->timeout = 30;

$messages = [131, 132, 134];

$result = $api->call('calls.broadcast_1', [$target, $messages, null]);
```

*Method*
* [calls.broadcast_1](http://thecallr.com/docs/api/services/calls/#calls.broadcast_1)

*Objects*
* [Target](http://thecallr.com/docs/objects/#Target)
* [Calls.Broadcast1.Options](http://thecallr.com/docs/objects/#Calls.Broadcast1.Options)
