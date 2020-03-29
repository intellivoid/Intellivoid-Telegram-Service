<?php /** @noinspection PhpUnused */

    namespace Longman\TelegramBot\Commands\SystemCommands;

    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\InvalidSearchMethodException;
    use IntellivoidAccounts\Exceptions\TelegramClientNotFoundException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\TelegramClient\Chat;
    use IntellivoidAccounts\Objects\TelegramClient\User;
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
         * @throws DatabaseException
         * @throws InvalidSearchMethodException
         * @throws TelegramClientNotFoundException
         */
        public function execute()
        {
            $IntellivoidAccounts = new IntellivoidAccounts();

            $Client = $IntellivoidAccounts->getTelegramClientManager()->registerClient(
                Chat::fromArray($this->getMessage()->getChat()->getRawData()),
                User::fromArray($this->getMessage()->getFrom()->getRawData())
            );

            $text = "This is the official Intellivoid Services bot for Telegram\n\n";
            $text .= "You can link your Telegram account to your Intellivoid account using this bot ";
            $text .= "to receive security alerts, approve login requests and or receive notifications from ";
            $text .= "third party applications that are linked to your Intellivoid Account";

            return Request::sendMessage([
                'chat_id'      => $this->getMessage()->getChat()->getId(),
                'text'         => $text,
                'reply_markup' => new InlineKeyboard([
                    ['text' => 'Link your Intellivoid Account', 'url' => "https://accounts.intellivoid.net/auth/telegram?auth=telegram&client_id=" . $Client->PublicID]
                ]),
            ]);
        }
    }