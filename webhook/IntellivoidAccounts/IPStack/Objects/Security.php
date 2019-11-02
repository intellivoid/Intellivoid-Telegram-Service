<?php


    namespace IPStack\Objects;

    use IPStack\Abstracts\CrawlerType;
    use IPStack\Abstracts\ProxyType;
    use IPStack\Abstracts\ThreatLevel;

    /**
     * Class Security
     * @package IPStack\Objects
     */
    class Security
    {
        /**
         * 	Returns true or false depending on whether or not the given IP is associated with a proxy.
         *
         * @var bool
         */
        public $IsProxy;

        /**
         * 	Returns the type of proxy the IP is associated with.
         *
         * @var string|ProxyType
         */
        public $ProxyType;

        /**
         * Returns true or false depending on whether or not the given IP is associated with a crawler.
         *
         * @var bool
         */
        public $IsCrawler;

        /**
         * Returns the name of the crawler the IP is associated with.
         *
         * @var string
         */
        public $CrawlerName;

        /**
         * Returns the type of crawler the IP is associated with.
         *
         * @var string|CrawlerType
         */
        public $CrawlerType;

        /**
         * Returns true or false depending on whether or not the given IP is associated with the anonymous Tor system.
         *
         * @var bool
         */
        public $IsTor;

        /**
         * 	Returns the type of threat level the IP is associated with.
         *
         * @var string|ThreatLevel
         */
        public $ThreatLevel;

        /**
         * Returns an object containing all threat types associated with the IP.
         *
         * @var array(ThreatType)
         */
        public $ThreatTypes;

        /**
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'is_proxy' => $this->IsProxy,
                'proxy_type' => $this->ProxyType,
                'is_crawler' => $this->IsCrawler,
                'crawler_type' => $this->CrawlerType,
                'is_tor' => $this->IsTor,
                'threat_level' => $this->ThreatLevel,
                'threat_types' => $this->ThreatTypes
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return Security
         */
        public static function fromArray(array $data): Security
        {
            $SecurityObject = new Security();

            if(isset($data['is_proxy']))
            {
                $SecurityObject->IsProxy = (bool)$data['is_proxy'];
            }

            if(isset($data['proxy_type']))
            {
                $SecurityObject->ProxyType = $data['proxy_type'];
            }

            if(isset($data['is_crawler']))
            {
                $SecurityObject->IsCrawler = (bool)$data['is_crawler'];
            }

            if(isset($data['crawler_name']))
            {
                $SecurityObject->CrawlerName = $data['crawler_name'];
            }

            if(isset($data['crawler_type']))
            {
                $SecurityObject->CrawlerType = $data['crawler_type'];
            }

            if(isset($data['is_tor']))
            {
                $SecurityObject->IsTor = (bool)$data['is_tor'];
            }

            if(isset($data['threat_level']))
            {
                $SecurityObject->ThreatLevel = $data['threat_level'];
            }

            if(isset($data['threat_types']))
            {
                $SecurityObject->ThreatTypes = $data['threat_types'];
            }

            return $SecurityObject;
        }
    }