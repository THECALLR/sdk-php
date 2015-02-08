<?php

namespace THECALLR\Realtime;

/**
 * Real-time commands
 * @author Florent CHAUVEAU <fc@thecallr.com>
 * @todo add @return values
 */
class Command
{
    public $command;
    public $params;

    public function __construct($command, $params)
    {
        $this->command = $command;
        $this->params = $params;
    }

    /**
     * `play` command
     * @param string|int $media Media to play (media id (int) or text-to-speech)
     * @see http://thecallr.com/docs/real-time/#play
     * @example Command::play(42)
     * @example Command::play('TTS|TTS_EN-GB_SERENA|Hello there')
     */
    public static function play($media)
    {
        return new Command('play', [$media]);
    }

    /**
     * `play_record` command
     * @param string $mediaFile Recording file to play
     * @see http://thecallr.com/docs/real-time/#play_record
     * @example Command::playRecord($recordingFile)
     */
    public static function playRecord($mediaFile)
    {
        return new Command('play_record', [$mediaFile]);
    }

    /**
     * `play_wav_data` command
     * @param string $wavData Base64 encoded WAV data to play
     * @see http://thecallr.com/docs/real-time/#play_wav_data
     * @example Command::playWavData($wavData)
     */
    public static function playWavData($wavData)
    {
        return new Command('play_wav_data', [$wavData]);
    }

    /**
     * `read` command
     * @param int $attempts Maximum attempts. min:1 max:10
     * @param int $maxDigits Maximum digits. min:1 max:20
     * @param string $media Prompt message
     * @param int $timeoutMs Input timeout in milliseconds. min:100 max:30000
     * @see http://thecallr.com/docs/real-time/#read
     * @example Command::read(3, 11, 'TTS|TTS_EN-GB_SERENA|Hello. Please enter your phone number.', 10000)
     */
    public static function read($attempts, $maxDigits, $media, $timeoutMs)
    {
        return new Command('read', [$attempts, $maxDigits, $media, $timeoutMs]);
    }

    /**
     * `record` command
     * @param int $maxDuration (seconds) Maximum recording duration. Min:0 (disabled), Max:300.
     * @param int $silence (seconds) Stop recording on silence. Min:0 (disabled), Max:20.
     * @see http://thecallr.com/docs/real-time/#record
     * @example Command::record(30, 0)
     */
    public static function record($maxDuration, $silence)
    {
        return new Command('record', [$maxDuration, $silence]);
    }

    /**
     * `send_dtmf` command
     * @param string $digits Digits to send (0-9, *, #). Example : "123#"
     * @param int $durationMs (milliseconds) Duration of each digit. Min: 1, Max: 5000.
     * @param int $timeoutMs (milliseconds) Amount of time between tones. Min: 0, Max: 10000.
     * @see http://thecallr.com/docs/real-time/#send_dtmf
     * @example Command::sendDTMF(123#, 500, 500)
     */
    public static function sendDTMF($maxDuration, $silence)
    {
        return new Command('send_dtmf', [$maxDuration, $silence]);
    }
}
