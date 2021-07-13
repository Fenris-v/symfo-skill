<?php

namespace App\Service;

use App\Helpers\LoggerTrait;
use Http\Client\Exception;
use Nexy\Slack\Client;
use Nexy\Slack\Exception\SlackApiException;

class SlackClient
{
    use LoggerTrait;

    /**
     * SlackClient constructor.
     */
    public function __construct(private Client $slack)
    {
    }

    /**
     * @param string $message
     * @param string $icon
     * @param string $from
     * @throws Exception
     * @throws SlackApiException
     */
    public function send(
        string $message,
        string $icon = ':ghost:',
        string $from = 'Symfo'
    ): void {
        $this->logInfo('Sended: ', ['message' => $message]);

        $slackMessage = $this->slack->createMessage();

        $slackMessage->from($from)
            ->withIcon($icon)
            ->setText($message);

        $this->slack->sendMessage($slackMessage);
    }
}
