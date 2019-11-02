<?php


    namespace IPStack\Objects;

    /**
     * Class IPAddress
     * @package IPStack\Objects
     */
    class IPAddress
    {
        /**
         * 	Returns the requested IP address.
         *
         * @var string
         */
        public $IP;

        /**
         * Returns the hostname the requested IP resolves to, only returned if Hostname Lookup is enabled.
         *
         * @var string
         */
        public $Hostname;

        /**
         * 	Returns the IP address type IPv4 or IPv6.
         *
         * @var string
         */
        public $Type;

        /**
         * Returns the 2-letter continent code associated with the IP.
         *
         * @var string
         */
        public $ContinentCode;

        /**
         * Returns the name of the continent associated with the IP.
         *
         * @var string
         */
        public $ContinentName;

        /**
         * Returns the 2-letter country code associated with the IP.
         *
         * @var string
         */
        public $CountryCode;

        /**
         * Returns the name of the country associated with the IP.
         *
         * @var string
         */
        public $CountryName;

        /**
         * Returns the region code of the region associated with the IP (e.g. CA for California).
         *
         * @var string
         */
        public $RegionCode;

        /**
         * Returns the name of the region associated with the IP.
         *
         * @var string
         */
        public $RegionName;

        /**
         * 	Returns the name of the city associated with the IP.
         *
         * @var string
         */
        public $City;

        /**
         * 	Returns the ZIP code associated with the IP.
         *
         * @var string
         */
        public $Zip;

        /**
         * 	Returns the latitude value associated with the IP.
         *
         * @var float
         */
        public $Latitude;

        /**
         * Returns the longitude value associated with the IP.
         *
         * @var float
         */
        public $Longitude;

        /**
         * Returns multiple location-related objects
         *
         * @var Location
         */
        public $Location;

        /**
         * Returns an object containing timezone-related data.
         *
         * @var Timezone
         */
        public $Timezone;

        /**
         * Returns an object containing currency-related data.
         *
         * @var Currency
         */
        public $Currency;

        /**
         * Returns an object containing connection-related data.
         *
         * @var Connection
         */
        public $Connection;

        /**
         * Returns an object containing security-related data.
         *
         * @var Security
         */
        public $Security;

        /**
         * Creates array from object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'ip' => $this->IP,
                'host_name' => $this->Hostname,
                'type' => $this->Type,
                'continent_code' => $this->ContinentCode,
                'continent_name' => $this->ContinentName,
                'country_code' => $this->CountryCode,
                'country_name' => $this->CountryName,
                'region_code' => $this->RegionCode,
                'region_name' => $this->RegionName,
                'city' => $this->City,
                'zip' => $this->Zip,
                'latitude' => $this->Latitude,
                'longitude' => $this->Longitude,
                'location' => $this->Location->toArray(),
                'timezone' => $this->Timezone->toArray(),
                'currency' => $this->Currency->toArray(),
                'connection' => $this->Connection->toArray(),
                'security' => $this->Security->toArray()
            );
        }

        /**
         * Creates the object from array
         *
         * @param array $data
         * @return IPAddress
         */
        public static function fromArray(array $data): IPAddress
        {
            $IPAddressObject = new IPAddress();

            if(isset($data['ip']))
            {
                $IPAddressObject->IP = $data['ip'];
            }

            if(isset($data['hostname']))
            {
                $IPAddressObject->Hostname = $data['hostname'];
            }

            if(isset($data['type']))
            {
                $IPAddressObject->Type = $data['type'];
            }

            if(isset($data['continent_code']))
            {
                $IPAddressObject->ContinentCode = $data['continent_code'];
            }

            if(isset($data['continent_name']))
            {
                $IPAddressObject->ContinentName = $data['continent_name'];
            }

            if(isset($data['country_code']))
            {
                $IPAddressObject->CountryCode = $data['country_code'];
            }

            if(isset($data['country_name']))
            {
                $IPAddressObject->CountryName = $data['country_name'];
            }

            if(isset($data['region_code']))
            {
                $IPAddressObject->RegionCode = $data['region_code'];
            }

            if(isset($data['region_name']))
            {
                $IPAddressObject->RegionName = $data['region_name'];
            }

            if(isset($data['city']))
            {
                $IPAddressObject->City = $data['city'];
            }

            if(isset($data['zip']))
            {
                $IPAddressObject->Zip = $data['zip'];
            }

            if(isset($data['latitude']))
            {
                $IPAddressObject->Latitude = $data['latitude'];
            }

            if(isset($data['longitude']))
            {
                $IPAddressObject->Longitude = $data['longitude'];
            }

            if(isset($data['location']))
            {
                $IPAddressObject->Location = Location::fromArray($data['location']);
            }
            else
            {
                $IPAddressObject->Location = new Location();
            }

            if(isset($data['timezone']))
            {
                $IPAddressObject->Timezone = Timezone::fromArray($data['timezone']);
            }
            else
            {
                $IPAddressObject->Timezone = new Timezone();
            }

            if(isset($data['currency']))
            {
                $IPAddressObject->Currency = Currency::fromArray($data['currency']);
            }
            else
            {
                $IPAddressObject->Currency = new Currency();
            }

            if(isset($data['connection']))
            {
                $IPAddressObject->Connection = Connection::fromArray($data['connection']);
            }
            else
            {
                $IPAddressObject->Connection = new Connection();
            }

            if(isset($data['security']))
            {
                $IPAddressObject->Security = Security::fromArray($data['security']);
            }
            else
            {
                $IPAddressObject->Security = new Security();
            }

            return $IPAddressObject;
        }
    }