<?php


    namespace IPStack;

    use IPStack\Exceptions\LookupException;
    use IPStack\Objects\IPAddress;

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'CrawlerType.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'ProxyType.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'ThreatLevel.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Abstracts' . DIRECTORY_SEPARATOR . 'ThreatType.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Exceptions' . DIRECTORY_SEPARATOR . 'LookupException.php');

    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Connection.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Currency.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'IPAddress.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Language.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Location.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Security.php');
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'Objects' . DIRECTORY_SEPARATOR . 'Timezone.php');

    /**
     * Class IPStack
     * @package IPStack
     */
    class IPStack
    {
        /**
         * @var string
         */
        private $AccessKey;

        /**
         * @var string
         */
        private $Host;

        /**
         * @var bool
         */
        private $UseSSL;

        /**
         * IPStack constructor.
         * @param string $access_key
         * @param bool $ssl
         * @param string $host
         */
        public function __construct(string $access_key, bool $ssl = false, string $host = "api.ipstack.com")
        {
            $this->Host = $host;
            $this->UseSSL = $ssl;
            $this->AccessKey = $access_key;
        }

        /**
         * Performs a lookup
         *
         * @param string $ip_address
         * @return IPAddress
         * @throws LookupException
         */
        public function lookup(string $ip_address): IPAddress
        {
            $Protocol = 'http';
            if($this->UseSSL == true)
            {
                $Protocol = 'https';
            }
            $ch = curl_init($Protocol . '://' . $this->Host . '/'. $ip_address . '?access_key='. $this->AccessKey);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

            $Results = json_decode(curl_exec($ch), true);
            curl_close($ch);

            if(isset($Results['success']))
            {
                if($Results['success'] == false)
                {
                    throw new LookupException($Results['error']['info'], (int)$Results['error']['code']);
                }
            }

           return IPAddress::fromArray($Results);
        }
    }