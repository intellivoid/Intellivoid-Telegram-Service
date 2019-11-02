<?php


    namespace IntellivoidAccounts\Objects;


    /**
     * Class LocationData
     * @package IntellivoidAccounts\Objects
     */
    class LocationData
    {
        /**
         * The Zip Code for this IP Address
         *
         * @var int|null
         */
        public $ZipCode;

        /**
         * The name of the continent
         *
         * @var string|null
         */
        public $ContinentName;

        /**
         * The code of the continent
         *
         * @var string|null
         */
        public $ContinentCode;

        /**
         * The name of the country
         *
         * @var string|null
         */
        public $CountryName;

        /**
         * The country code
         *
         * @var string|null
         */
        public $CountryCode;

        /**
         * The city
         *
         * @var string|null
         */
        public $City;

        /**
         * The Longitude
         *
         * @var string|null
         */
        public $Longitude;

        /**
         * The Latitude
         *
         * @var string|null
         */
        public $Latitude;

        /**
         * The Unix Timestamp of when this data was last updated
         *
         * @var int
         */
        public $LastUpdated;

        /**
         * Returns an array that represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'zip_code' => $this->ZipCode,
                'continent_name' => $this->ContinentName,
                'continent_code' => $this->ContinentCode,
                'country_name' => $this->CountryName,
                'country_code' => $this->CountryCode,
                'city' => $this->City,
                'longitude' => $this->Longitude,
                'latitude' => $this->Latitude,
                'last_updated' => $this->LastUpdated
            );
        }

        /**
         * Creates object from array data
         *
         * @param array $data
         * @return LocationData
         */
        public static function fromArray(array $data): LocationData
        {
            $LocationDataObject = new LocationData();

            if(isset($data['zip_code']))
            {
                $LocationDataObject->ZipCode = $data['zip_code'];
            }

            if(isset($data['continent_name']))
            {
                $LocationDataObject->ContinentName = $data['continent_name'];
            }

            if(isset($data['country_name']))
            {
                $LocationDataObject->CountryName = $data['country_name'];
            }

            if(isset($data['country_code']))
            {
                $LocationDataObject->CountryCode = $data['country_code'];
            }

            if(isset($data['city']))
            {
                $LocationDataObject->City = $data['city'];
            }

            if(isset($data['longitude']))
            {
                $LocationDataObject->Longitude = $data['longitude'];
            }

            if(isset($data['latitude']))
            {
                $LocationDataObject->Latitude = $data['latitude'];
            }

            if(isset($data['last_updated']))
            {
                $LocationDataObject->LastUpdated = (int)$data['last_updated'];
            }
            else
            {
                $LocationDataObject->LastUpdated = 0;
            }

            return $LocationDataObject;
        }
    }