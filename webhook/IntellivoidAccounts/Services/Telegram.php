<?php /** @noinspection DuplicatedCode */

/** @noinspection PhpUnused */


    namespace IntellivoidAccounts\Services;

    use Exception;
    use IntellivoidAccounts\Abstracts\SearchMethods\KnownHostsSearchMethod;
    use IntellivoidAccounts\Exceptions\AuthNotPromptedException;
    use IntellivoidAccounts\Exceptions\AuthPromptAlreadyApprovedException;
    use IntellivoidAccounts\Exceptions\AuthPromptExpiredException;
    use IntellivoidAccounts\Exceptions\DatabaseException;
    use IntellivoidAccounts\Exceptions\HostNotKnownException;
    use IntellivoidAccounts\Exceptions\InvalidIpException;
    use IntellivoidAccounts\Exceptions\InvalidUrlException;
    use IntellivoidAccounts\Exceptions\TelegramActionFailedException;
    use IntellivoidAccounts\Exceptions\TelegramApiException;
    use IntellivoidAccounts\Exceptions\TelegramServicesNotAvailableException;
    use IntellivoidAccounts\Exceptions\TooManyPromptRequestsException;
    use IntellivoidAccounts\IntellivoidAccounts;
    use IntellivoidAccounts\Objects\TelegramClient;
    use IntellivoidAccounts\Objects\UserAgent;
    use IntellivoidAccounts\Utilities\Validate;

    /**
     * Class Telegram
     * @package IntellivoidAccounts\Services
     */
    class Telegram
    {
        /**
         * @var IntellivoidAccounts
         */
        private $intellivoidAccounts;

        /**
         * Array of Emojis that are used for Telegram
         *
         * @var array
         */
        private $emojis;

        /**
         * Telegram constructor.
         * @param IntellivoidAccounts $intellivoidAccounts
         */
        public function __construct(IntellivoidAccounts $intellivoidAccounts)
        {
            $this->intellivoidAccounts = $intellivoidAccounts;
            $this->emojis = array(
                'BELL' => "\u{1F514}",
                'LOCK' => "\u{1F512}",
                'CHECK' => "\u{2705}",
                'DENY' => "\u{1F6AB}"
            );
        }

        /**
         * Sends a HTTP/HTTPs POST Request to the given location and returns the response as a string
         *
         * @param string $location
         * @param array $payload
         * @return string
         * @throws TelegramApiException
         */
        private function sendRequest(string $location, array $payload): string
        {
            try
            {
                $ch = curl_init($location);
                curl_setopt( $ch, CURLOPT_POSTFIELDS, json_encode($payload));
                curl_setopt( $ch, CURLOPT_HTTPHEADER, array('Content-Type:application/json'));
                curl_setopt( $ch, CURLOPT_RETURNTRANSFER, true );
                $result = curl_exec($ch);
                curl_close($ch);
            }
            catch(Exception $exception)
            {
                throw new TelegramApiException();
            }

            return $result;
        }

        /**
         * Builds the URL for the given endpoint via action
         *
         * @param string $action
         * @return string
         */
        private function getEndpoint(string $action): string
        {
            return "https://api.telegram.org/bot" . $this->intellivoidAccounts->getTelegramConfiguration()['TgBotToken'] . "/$action";
        }

        /**
         * @param TelegramClient $telegramClient
         * @param string $from
         * @param string $message
         * @param string|null $url
         * @return bool
         * @throws TelegramActionFailedException
         * @throws TelegramApiException
         * @throws TelegramServicesNotAvailableException
         * @throws InvalidUrlException
         * @throws DatabaseException
         */
        public function sendNotification(TelegramClient $telegramClient, string $from, string $message, string $url=null): bool
        {
            if(strtolower($this->intellivoidAccounts->getTelegramConfiguration()['TgBotEnabled']) !== "true")
            {
                throw new TelegramServicesNotAvailableException();
            }

            $keyboard = array();

            if($url == null)
            {
                if(Validate::url($url) == false)
                {
                    throw new InvalidUrlException();
                }

                $keyboard = array(
                    "inline_keyboard" => [
                        [array("text" => "Open URL", "url" => $url)],
                    ]
                );
            }

            $Response = json_decode($this->sendRequest($this->getEndpoint('sendMessage'), array(
                'chat_id' => $telegramClient->Chat->ID,
                'parse_mode' => 'html',
                'text' => $this->emojis['BELL'] . " <b>Notification from $from</b>\n\n$message",
                'reply_markup' => $keyboard
            )), true);

            /** @noinspection DuplicatedCode */
            if($Response['ok'] == false)
            {
                $Message = "unknown";
                $ErrorCode = 0;

                if(isset($Response['description']))
                {
                    $Message = $Response['description'];
                }

                if(isset($Response['error_code']))
                {
                    $ErrorCode = (int)$Response['error_code'];
                }

                $telegramClient->Available = false;
                $telegramClient->LastActivityTimestamp = (int)time();
                $this->intellivoidAccounts->getTelegramClientManager()->updateClient($telegramClient);

                throw new TelegramActionFailedException($Message, $ErrorCode);
            }

            $telegramClient->Available = true;
            $telegramClient->LastActivityTimestamp = (int)time();
            $this->intellivoidAccounts->getTelegramClientManager()->updateClient($telegramClient);

            return true;
        }

        /**
         * Prompts the user for authentication
         *
         * @param TelegramClient $telegramClient
         * @param string $username
         * @param string $user_agent
         * @param int $known_host_id
         * @return bool
         * @throws DatabaseException
         * @throws TelegramActionFailedException
         * @throws TelegramApiException
         * @throws TelegramServicesNotAvailableException
         * @throws TooManyPromptRequestsException
         * @throws HostNotKnownException
         * @throws InvalidIpException
         */
        public function promptAuth(TelegramClient $telegramClient, string $username, string $user_agent, int $known_host_id): bool
        {
            if(strtolower($this->intellivoidAccounts->getTelegramConfiguration()['TgBotEnabled']) !== "true")
            {
                throw new TelegramServicesNotAvailableException();
            }

            // Check the current attempts and expire sessions
            if($telegramClient->SessionData->keyExists('auth', 'current_attempts') == false)
            {
                $telegramClient->SessionData->setData('auth', 'current_attempts', 0);
            }
            else
            {
                $current_time = (int)time();
                /** @var int $attempts_reset */
                $attempts_reset = $telegramClient->SessionData->getData('auth', 'attempts_reset');
                /** @var int $current_attempts */
                $current_attempts = $telegramClient->SessionData->getData('auth', 'current_attempts');

                if($current_time > $attempts_reset)
                {
                    $telegramClient->SessionData->setData('auth', 'current_attempts', 0);
                }
                else
                {
                    if($current_attempts == 3)
                    {
                        throw new TooManyPromptRequestsException();
                    }
                    else
                    {
                        $current_attempts += 1;
                        $telegramClient->SessionData->setData('auth', 'current_attempts', $current_attempts);
                    }
                }
            }

            $telegramClient->SessionData->setData('auth', 'attempts_reset', (int)time() + 1800);
            $telegramClient->SessionData->setData('auth', 'currently_active', true);
            $telegramClient->SessionData->setData('auth', 'expires', (int)time() + 300);
            $telegramClient->SessionData->setData('auth', 'approved', false);

            $this->intellivoidAccounts->getTelegramClientManager()->updateClient($telegramClient);


            $user_agent_x = null;

            if(Validate::userAgent($user_agent))
            {
                $user_agent_x = UserAgent::fromString($user_agent);
            }
            else
            {
                $user_agent_x = new UserAgent();
                $user_agent_x->UserAgentString = "None";
            }

            $host = $this->intellivoidAccounts->getKnownHostsManager()->getHost(KnownHostsSearchMethod::byId, $known_host_id);

            $ip = $host->IpAddress;
            /** @noinspection PhpUnusedLocalVariableInspection */
            $country = "Unknown";
            $device = "Unknown";
            $browser = "Unknown";

            if($host->LocationData->CountryName == null)
            {
                $country = "Unknown";
            }
            else
            {
                if(isset($host->LocationData->City))
                {
                    $country = $host->LocationData->City;
                    $country .= ' ' . $host->LocationData->CountryName;
                }
                else
                {
                    $country = $host->LocationData->CountryName;
                }

                if(isset($host->LocationData->ZipCode))
                {
                    $country .= ' (' . $host->LocationData->ZipCode . ')';
                }
            }

            if($user_agent_x->Platform !== null)
            {
                $device = $user_agent_x->Platform;
            }

            if($user_agent_x->Browser !== null)
            {
                $browser = $user_agent_x->Browser;
            }

            $Response = json_decode($this->sendRequest($this->getEndpoint('sendMessage'), array(
                'chat_id' => $telegramClient->Chat->ID,
                'parse_mode' => 'html',
                'text' =>
                    $this->emojis['LOCK'] . " Hi " . $username . ", please confirm the authentication request\n\n" .
                    "<b>IP:</b> <code>$ip</code>\n" .
                    "<b>Country:</b> <code>$country</code>\n" .
                    "<b>Device:</b> <code>$device</code>\n" .
                    "<b>Browser:</b> <code>$browser</code>\n\n" .
                    "<i>If this was not you, click deny and change your password immediately</i>",
                    'reply_markup' =>  array(
                    "inline_keyboard" => [
                        [
                            array("text" => $this->emojis['DENY'] . ' Deny', "callback_data" => "auth_deny"),
                            array("text" => $this->emojis['CHECK'] . ' Authenticate', "callback_data" => "auth_allow")
                        ]
                    ]
                )
            )), true);

            /** @noinspection DuplicatedCode */
            if($Response['ok'] == false)
            {
                $Message = "unknown";
                $ErrorCode = 0;

                if(isset($Response['description']))
                {
                    $Message = $Response['description'];
                }

                if(isset($Response['error_code']))
                {
                    $ErrorCode = (int)$Response['error_code'];
                }

                $telegramClient->Available = false;
                $telegramClient->LastActivityTimestamp = (int)time();
                $this->intellivoidAccounts->getTelegramClientManager()->updateClient($telegramClient);

                throw new TelegramActionFailedException($Message, $ErrorCode);
            }

            return true;
        }

        /**
         * Checks if the state of the prompt is valid or not
         *
         * @param TelegramClient $telegramClient
         * @return bool
         * @throws AuthNotPromptedException
         * @throws AuthPromptExpiredException
         * @throws AuthPromptAlreadyApprovedException
         */
        private function checkPromptState(TelegramClient $telegramClient): bool
        {
            // Check the prompt status
            if($telegramClient->SessionData->keyExists('auth', 'attempts_reset') == false)
            {
                throw new AuthNotPromptedException();
            }

            if($telegramClient->SessionData->keyExists('auth', 'current_attempts') == false)
            {
                throw new AuthNotPromptedException();
            }

            if($telegramClient->SessionData->keyExists('auth', 'currently_active') == false)
            {
                throw new AuthNotPromptedException();
            }

            if($telegramClient->SessionData->keyExists('auth', 'expires') == false)
            {
                throw new AuthNotPromptedException();
            }

            if($telegramClient->SessionData->keyExists('auth', 'approved') == false)
            {
                throw new AuthNotPromptedException();
            }

            /** @var bool $currently_active */
            $currently_active = $telegramClient->SessionData->getData('auth', 'currently_active');
            /** @var int $expires */
            $expires = $telegramClient->SessionData->getData('auth', 'expires');
            /** @var bool $expires */
            $approved = $telegramClient->SessionData->getData('auth', 'approved');

            if($currently_active == false)
            {
                throw new AuthNotPromptedException();
            }

            if($approved == true)
            {
                throw new AuthPromptAlreadyApprovedException();
            }

            if((int)time() > $expires)
            {
                throw new AuthPromptExpiredException();
            }

            return true;
        }

        /**
         * Returns the state of the authentication method
         *
         * @param TelegramClient $telegramClient
         * @return array
         */
        private function getAuthPrompt(TelegramClient $telegramClient): array
        {
            /** @var int $attempts_reset */
            $attempts_reset = $telegramClient->SessionData->getData('auth', 'attempts_reset');
            /** @var int $current_attempts */
            $current_attempts = $telegramClient->SessionData->getData('auth', 'current_attempts');
            /** @var bool $currently_active */
            $currently_active = $telegramClient->SessionData->getData('auth', 'currently_active');
            /** @var int $expires */
            $expires = $telegramClient->SessionData->getData('auth', 'expires');
            /** @var bool $approved */
            $approved = $telegramClient->SessionData->getData('auth', 'approved');

            return array(
                'attempts_reset' => (int)$attempts_reset,
                'current_attempts' => (int)$current_attempts,
                'currently_active' => (bool)$currently_active,
                'expires' => (int)$expires,
                'approved' => (bool)$approved
            );
        }

        /**
         * Updates the authentication prompt via properties
         *
         * @param TelegramClient $telegramClient
         * @param array $properties
         * @return TelegramClient
         */
        private function updateAuthPrompt(TelegramClient $telegramClient, array $properties): TelegramClient
        {
            $telegramClient->SessionData->setData('auth', 'attempts_reset', (int)$properties['attempts_reset']);
            $telegramClient->SessionData->setData('auth', 'current_attempts', (int)$properties['current_attempts']);
            $telegramClient->SessionData->setData('auth', 'currently_active', (bool)$properties['currently_active']);
            $telegramClient->SessionData->setData('auth', 'expires', (int)$properties['expires']);
            $telegramClient->SessionData->setData('auth', 'approved', (bool)$properties['approved']);

            return $telegramClient;
        }

        /**
         * Approves of the auth prompt and updates the state
         *
         * @param TelegramClient $telegramClient
         * @throws AuthNotPromptedException
         * @throws AuthPromptAlreadyApprovedException
         * @throws AuthPromptExpiredException
         * @throws TelegramServicesNotAvailableException
         * @throws DatabaseException
         */
        public function approveAuth(TelegramClient $telegramClient)
        {
            if(strtolower($this->intellivoidAccounts->getTelegramConfiguration()['TgBotEnabled']) !== "true")
            {
                throw new TelegramServicesNotAvailableException();
            }

            $this->checkPromptState($telegramClient);
            $AuthPrompt = $this->getAuthPrompt($telegramClient);

            $AuthPrompt['approved'] = true;
            $AuthPrompt['currently_active'] = false;
            $AuthPrompt['current_attempts'] = 0;
            $AuthPrompt['attempts_reset'] = (int)time() + 1800;

            $this->intellivoidAccounts->getTelegramClientManager()->updateClient(
                $this->updateAuthPrompt($telegramClient, $AuthPrompt)
            );
        }

        /**
         * Disallows the auth prompt and updates the state
         *
         * @param TelegramClient $telegramClient
         * @throws AuthNotPromptedException
         * @throws AuthPromptAlreadyApprovedException
         * @throws AuthPromptExpiredException
         * @throws DatabaseException
         * @throws TelegramServicesNotAvailableException
         */
        public function disallowAuth(TelegramClient $telegramClient)
        {
            if(strtolower($this->intellivoidAccounts->getTelegramConfiguration()['TgBotEnabled']) !== "true")
            {
                throw new TelegramServicesNotAvailableException();
            }

            $this->checkPromptState($telegramClient);
            $AuthPrompt = $this->getAuthPrompt($telegramClient);

            $AuthPrompt['approved'] = false;
            $AuthPrompt['currently_active'] = false;

            $this->intellivoidAccounts->getTelegramClientManager()->updateClient(
                $this->updateAuthPrompt($telegramClient, $AuthPrompt)
            );
        }

        /**
         * Polls the auth prompt and determines if the prompt has been approved or not
         *
         * @param TelegramClient $telegramClient
         * @return bool
         * @throws AuthNotPromptedException
         * @throws AuthPromptExpiredException
         * @throws TelegramServicesNotAvailableException
         */
        public function pollAuthPrompt(TelegramClient $telegramClient): bool
        {
            if(strtolower($this->intellivoidAccounts->getTelegramConfiguration()['TgBotEnabled']) !== "true")
            {
                throw new TelegramServicesNotAvailableException();
            }

            // Check the prompt status
            if($telegramClient->SessionData->keyExists('auth', 'attempts_reset') == false)
            {
                throw new AuthNotPromptedException();
            }

            if($telegramClient->SessionData->keyExists('auth', 'current_attempts') == false)
            {
                throw new AuthNotPromptedException();
            }

            if($telegramClient->SessionData->keyExists('auth', 'currently_active') == false)
            {
                throw new AuthNotPromptedException();
            }

            if($telegramClient->SessionData->keyExists('auth', 'expires') == false)
            {
                throw new AuthNotPromptedException();
            }

            if($telegramClient->SessionData->keyExists('auth', 'approved') == false)
            {
                throw new AuthNotPromptedException();
            }

            /** @var bool $currently_active */
            $currently_active = $telegramClient->SessionData->getData('auth', 'currently_active');
            /** @var int $expires */
            $expires = $telegramClient->SessionData->getData('auth', 'expires');
            /** @var bool $expires */
            $approved = $telegramClient->SessionData->getData('auth', 'approved');

            if($currently_active == false)
            {
                throw new AuthNotPromptedException();
            }

            if((int)time() > $expires)
            {
                throw new AuthPromptExpiredException();
            }

            if($approved == true)
            {
                return true;
            }

            return false;
        }
    }