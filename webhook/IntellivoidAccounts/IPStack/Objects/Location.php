<?php


    namespace IPStack\Objects;

    /**
     * Class Location
     * @package IPStack\Objects
     */
    class Location
    {
        /**
         * Returns the unique geoname identifier in accordance with the Geonames Registry.
         *
         * @var int
         */
        public $GeoNameID;

        /**
         * Returns the capital city of the country associated with the IP.
         *
         * @var string
         */
        public $Capital;

        /**
         * Returns an object containing one or multiple sub-objects per language spoken in the country associated with the IP.
         *
         * @var array|Language
         */
        public $Languages;

        /**
         * 	Returns an HTTP URL leading to an SVG-flag icon for the country associated with the IP.
         *
         * @var string
         */
        public $CountryFlag;

        /**
         * Returns the unicode value of the emoji icon for the flag of the country associated with the IP. (e.g. U+1F1F5 U+1F1F9 for the Portuguese flag)
         *
         * @var string
         */
        public $CountryFlagEmoji;

        /**
         * Returns the unicode value of the emoji icon for the flag of the country associated with the IP. (e.g. U+1F1F5 U+1F1F9 for the Portuguese flag)
         *
         * @var string
         */
        public $CountryFlagEmojiUnicode;

        /**
         * Returns the calling/dial code of the country associated with the IP. (e.g. 351) for Portugal.
         *
         * @var int
         */
        public $CallingCode;

        /**
         * Returns true or false depending on whether or not the county associated with the IP is in the European Union.
         *
         * @var bool
         */
        public $IsEU;

        /**
         * Returns array of object
         *
         * @return array
         */
        public function toArray(): array
        {
            $Languages = array();

            if(count($Languages) > 0)
            {
                /** @var Language $language */
                foreach($Languages as $language)
                {
                    $Languages[] = $language->toArray();
                }
            }

            return array(
                'geoname_id' => $this->GeoNameID,
                'capital' => $this->Capital,
                'languages' => $Languages,
                'country_flag' => $this->CountryFlag,
                'country_flag_emoji' => $this->CountryFlagEmoji,
                'country_flag_emoji_unicode' => $this->CountryFlagEmojiUnicode,
                'calling_code' => $this->CallingCode,
                'is_eu' => $this->IsEU
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return Location
         */
        public static function fromArray(array $data): Location
        {
            $LocationObject = new Location();

            if(isset($data['geoname_id']))
            {
                $LocationObject->GeoNameID = $data['geoname_id'];
            }

            if(isset($data['capital']))
            {
                $LocationObject->Capital = $data['capital'];
            }

            $LocationObject->Languages = array();
            if(isset($data['languages']))
            {
                foreach($data['languages'] as $language)
                {
                    $LocationObject->Languages[] = Language::fromArray($language);
                }
            }

            if(isset($data['country_flag']))
            {
                $LocationObject->CountryFlag = $data['country_flag'];
            }

            if(isset($data['country_flag_emoji']))
            {
                $LocationObject->CountryFlagEmoji = $data['country_flag_emoji'];
            }

            if(isset($data['country_flag_emoji_unicode']))
            {
                $LocationObject->CountryFlagEmojiUnicode = $data['country_flag_emoji_unicode'];
            }

            if(isset($data['calling_code']))
            {
                $LocationObject->CallingCode = (int)$data['calling_code'];
            }

            if(isset($data['is_eu']))
            {
                $LocationObject->IsEU = (bool)$data['is_eu'];
            }

            return $LocationObject;
        }
    }