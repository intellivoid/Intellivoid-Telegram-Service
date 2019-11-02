<?php


    namespace IntellivoidAccounts\Objects;

    use Exception;
    use IntellivoidAccounts\Utilities\Parse;

    /**
     * Class UserAgent
     * @package IntellivoidAccounts\Objects
     */
    class UserAgent
    {
        /**
         * The full user agent string
         *
         * @var string
         */
        public $UserAgentString;

        /**
         * The platform that was detected
         *
         * @var string|null
         */
        public $Platform;

        /**
         * The browser that was detected
         *
         * @var string|null
         */
        public $Browser;

        /**
         * The version that was detected
         *
         * @var string|null
         */
        public $Version;

        /**
         * Returns an array that represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'user_agent_string' => $this->UserAgentString,
                'platform' => $this->Platform,
                'browser' => $this->Browser,
                'version' => $this->Version
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return UserAgent
         */
        public static function fromArray(array $data): UserAgent
        {
            $UserAgentObject = new UserAgent();

            if(isset($data['user_agent_string']))
            {
                $UserAgentObject->UserAgentString = $data['user_agent_string'];
            }

            if(isset($data['platform']))
            {
                $UserAgentObject->Platform = $data['platform'];
            }

            if(isset($data['browser']))
            {
                $UserAgentObject->Browser = $data['browser'];
            }

            if(isset($data['version']))
            {
                $UserAgentObject->Version = $data['version'];
            }

            return $UserAgentObject;
        }

        /**
         * Creates object from user agent string
         *
         * @param string $user_agent
         * @return UserAgent
         */
        public static function fromString(string $user_agent): UserAgent
        {
            $UserAgentObject = new UserAgent();
            $ParsedData = null;

            try
            {
                $ParsedData = Parse::parse_user_agent($user_agent);
            }
            catch(Exception $exception)
            {
                $ParsedData = array();
            }

            $UserAgentObject->UserAgentString = $user_agent;

            if(isset($ParsedData['platform']))
            {
                $UserAgentObject->Platform = $ParsedData['platform'];
            }

            if(isset($ParsedData['browser']))
            {
                $UserAgentObject->Browser = $ParsedData['browser'];
            }

            if(isset($ParsedData['version']))
            {
                $UserAgentObject->Version = $ParsedData['version'];
            }

            return $UserAgentObject;
        }
    }