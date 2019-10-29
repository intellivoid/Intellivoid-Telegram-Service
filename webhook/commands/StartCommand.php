<?php /** @noinspection PhpUnused */

    namespace Longman\TelegramBot\Commands\SystemCommands;

    use Longman\TelegramBot\Commands\SystemCommand;
    use Longman\TelegramBot\Entities\InlineKeyboard;
    use Longman\TelegramBot\Entities\ServerResponse;
    use Longman\TelegramBot\Exception\TelegramException;
    use Longman\TelegramBot\Request;

    /**
     * Start command
     *
     * Gets executed when a user first starts using the bot.
     */
    class StartCommand extends SystemCommand
    {
        /**
         * @var string
         */
        protected $name = 'start';

        /**
         * @var string
         */
        protected $description = 'Start command';

        /**
         * @var string
         */
        protected $usage = '/start';

        /**
         * @var string
         */
        protected $version = '1.0.0';

        /**
         * @var bool
         */
        protected $private_only = true;

        /**
         * Command execute method
         *
         * @return ServerResponse
         * @throws TelegramException
         */
        public function execute()
        {
            $message = $this->getMessage();
            $chat_id = $message->getChat()->getId();

            $text = "This is the official Intellivoid Services bot for Telegram\n\n";
            $text .= "You can link your Telegram account to your Intellivoid account using this bot ";
            $text .= "to receive security alerts, approve login requests and or receive notifications from ";
            $text .= "third party applications that are linked to your Intellivoid Account";

            return Request::sendMessage([
                'chat_id'      => $this->getMessage()->getChat()->getId(),
                'text'         => $text,
                'reply_markup' => new InlineKeyboard([
                    ['text' => 'Link your Intellivoid Account', 'callback_data' => 'link_account']
                ]),
            ]);
        }
    }