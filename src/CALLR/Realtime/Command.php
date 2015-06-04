<?php

namespace CALLR\Realtime;

use \CALLR\Realtime\Command\ConferenceParams;

/**
 * Real-time commands
 * @author Florent CHAUVEAU <fc@callr.com>
 * @todo add @return values
 */
class Command
{
    public $command;
    public $params = [];

    /**
     * Constructor
     * @param  string $command Real-time command
     * @param  mixed[] $params Real-time command params
     */
    public function __construct($command, array $params = [])
    {
        $this->command = (string) $command;
        $this->params = (object) $params;
    }

    /**
     * `conference` command
     * @param  integer $id Conference id
     * @param  ConferenceParams $params Conference params
     */
    public static function conference($id, ConferenceParams $params)
    {
        $params = $params->getParams();
        $params['id'] = $id;
        return new Command('conference', $params);
    }

    /**
     * `dialout` command
     * @param  \CALLR\Objects\Target $targets Targets to call sequentially until one picks up
     * @param  string $cli Calling Line Identifier, 'BLOCKED' or '+CCNSN'
     * @param  string $ringtone 'RING' or 'MUSIC'
     * @param  string|integer $whisper Whisper media to callee
     * @param  string $cdrField Custom field written in CDR
     */
    public static function dialout(
        array $targets,
        $cli = 'BLOCKED',
        $ringtone = 'RING',
        $whisper = 0,
        $cdrField = ''
    ) {
        return new Command('dialout', ['targets'    => $targets,
                                       'cli'        => $cli,
                                       'ringtone'   => $ringtone,
                                       'whisper'    => $whisper,
                                       'cdr_field'  => $cdrField]);
    }

    /**
     * `hangup` command
     */
    public static function hangup()
    {
        return new Command('hangup');
    }

    /**
     * `hangup_callid` command
     * @param  int $callid The callid to hangup
     */
    public static function hangupCallID($callid)
    {
        return new Command('hangup_callid', ['callid' => $callid]);
    }

    /**
     * `play` command
     * @param string|integer $media Media to play (media id (int) or text-to-speech)
     * @see http://thecallr.com/docs/real-time/#play
     * @example Command::play(42)
     * @example Command::play('TTS|TTS_EN-GB_SERENA|Hello there')
     */
    public static function play($media)
    {
        return new Command('play', ['media_id' => $media]);
    }

    /**
     * `play_record` command
     * @param string $mediaFile Recording file to play
     * @see http://thecallr.com/docs/real-time/#play_record
     * @example Command::playRecord($recordingFile)
     */
    public static function playRecord($mediaFile)
    {
        return new Command('play_record', ['media_file' => $mediaFile]);
    }

    /**
     * `play_wav_data` command
     * @param string $wavData Base64 encoded WAV data to play
     * @see http://thecallr.com/docs/real-time/#play_wav_data
     * @example Command::playWavData($wavData)
     */
    public static function playWavData($wavData)
    {
        return new Command('play_wav_data', ['audio_data' => $wavData]);
    }

    /**
     * `read` command
     * @param integer $attempts Maximum attempts. min:1 max:10
     * @param integer $maxDigits Maximum digits. min:1 max:20
     * @param integer|string $media Prompt message
     * @param integer $timeoutMs Input timeout in milliseconds. min:100 max:30000
     * @see http://thecallr.com/docs/real-time/#read
     * @example Command::read(3, 11, 'TTS|TTS_EN-GB_SERENA|Hello. Please enter your phone number.', 10000)
     */
    public static function read($media, $maxDigits = 20, $attempts = 10, $timeoutMs = 30000)
    {
        return new Command('read', ['attempts'   => $attempts,
                                    'max_digits' => $maxDigits,
                                    'media_id'   => $media,
                                    'timeout_ms' => $timeoutMs]);
    }

    /**
     * `record` command
     * @param integer $maxDuration (seconds) Maximum recording duration. Min:0 (disabled), Max:300.
     * @param integer $silence (seconds) Stop recording on silence. Min:0 (disabled), Max:20.
     * @see http://thecallr.com/docs/real-time/#record
     * @example Command::record(30, 0)
     */
    public static function record($maxDuration, $silence = 0)
    {
        return new Command('record', ['max_duration' => $maxDuration, 'silence' => $silence]);
    }

    /**
     * `send_dtmf` command
     * @param string $digits Digits to send (0-9, *, #). Example : "123#"
     * @param integer $durationMs (milliseconds) Duration of each digit. Min: 1, Max: 5000.
     * @param integer $timeoutMs (milliseconds) Amount of time between tones. Min: 0, Max: 10000.
     * @see http://thecallr.com/docs/real-time/#send_dtmf
     * @example Command::sendDTMF(123#, 500, 1000)
     */
    public static function sendDTMF($digits, $durationMs = 500, $timeoutMs = 500)
    {
        return new Command('send_dtmf', ['digits'      => $digits,
                                         'duration_ms' => $durationMs,
                                         'timeout_ms'  => $timeoutMs]);
    }

    /**
     * `simple_conference` command
     * @param  integer $id Conference ID
     * @param  boolean $autoLeaveWhenAlone Automatically leave the conference room when
     *     you're the last participant. Only applies when someone leaves -
     *     it does not apply when you are joining and you are first.
     */
    public static function simpleConference($id, $autoLeaveWhenAlone = true)
    {
        return new Command('simple_conference', ['id' => $id,
                                                 'auto_leave_when_alone' => $autoLeaveWhenAlone]);
    }

    /**
     * `start_call_recording` command
     * @param  integer|string $announce Media to announce the call is being recorded. Set to 0 to ignore.
     */
    public static function startCallRecording($announce = 0)
    {
        return new Command('start_call_recording', ['announce' => $announce]);
    }

    /**
     * `stop_call_recording` command
     * @param  integer|string $announce Media to announce the call is not being recorded anymore. Set to 0 to ignore.
     */
    public static function stopCallRecording($announce = 0)
    {
        return new Command('stop_call_recording', ['announce' => $announce]);
    }

    /**
     * `wait` command
     * @param  integer $seconds Seconds to wait. 1..30
     */
    public static function wait($seconds = 1)
    {
        return new Command('wait', ['seconds' => $seconds]);
    }

    /**
     * `wait_for_silence` command
     * @param  integer $silenceMs  Minimum silence duration (milliseconds). 1..5000
     * @param  integer $iterations Number of times to try. 1..3
     * @param  integer $timeout    Global timeout if silence is not detected. 0..300
     */
    public static function waitForSilence($silenceMs, $iterations, $timeout)
    {
        return new Command('wait_for_silence', ['silence_ms' => $silenceMs,
                                                'iterations' => $iterations,
                                                'timeout'    => $timeout]);
    }
}
