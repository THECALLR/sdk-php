<?php

namespace CALLR\Realtime\Command;

/**
 * `conference` command parameters
 * @author Florent CHAUVEAU <fc@callr.com>
 */
class ConferenceParams extends Params
{
    /**
     * @var string $welcome Media played before joining the conference (and before optional pinPrompt)
     * @var string $waiting Media played in a loop while there is only one participant.
     * @var string $pinPrompt Media played to ask for a PIN code. Will be repeated until a PIN is entered.
     * @var string $pinError Media played when the PIN is invalid.
     * @var string $pinValid Media played when the PIN is valid.
     * @var string $pinCode PIN code to ask for. Callee must press # after the PIN. Leave empty for auto-joining. [0-9]
     * @var integer $pinMaxTries How many times do we ask for the PIN? [1..10]
     * @var integer $pinWait The number of seconds to wait for a digit response. [0..30]
     * @var boolean $autoLeaveWhenAlone Automatically leave the conference room when you're the last participant.
     *                                  Only applies when someone leaves - it does not apply when you are joining
     *                                  and you are first.
     * @var string $autoLeaveAnnounce Media played when leaving the conference room because you are the last participant
     * @var string $userJoin Media played when someone enters the conference room
     * @var string $userLeave Media played when someone leaves the conference room
     */
    public $autoLeaveAnnounce = 0;
    public $autoLeaveWhenAlone = false;
    public $pinCode = '';
    public $pinError = 0;
    public $pinMaxTries = 3;
    public $pinPrompt = 0;
    public $pinValid = 0;
    public $pinWait = 5;
    public $userJoin = 0;
    public $userLeave = 0;
    public $waiting = 0;
    public $welcome = 0;
}
