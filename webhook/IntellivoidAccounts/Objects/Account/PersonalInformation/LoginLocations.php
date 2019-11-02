<?php


    namespace IntellivoidAccounts\Objects\Account\PersonalInformation;

    /**
     * Class LoginLocations
     * @package IntellivoidAccounts\Objects\Account\PersonalInformation
     */
    class LoginLocations
    {
        /**
         * Known locations that are tracked
         *
         * @var array
         */
        public $KnownLocations;

        /**
         * LoginLocations constructor.
         */
        public function __construct()
        {
            $this->KnownLocations = [];
        }

        /**
         * Tracks the known location to be associated with this account
         *
         * @param string $LocationCode
         */
        public function addKnownLocation(string $LocationCode)
        {
            if(isset($this->KnownLocations) == false)
            {
                $this->KnownLocations[strtoupper($LocationCode)] = 0;
            }

            $this->KnownLocations[strtoupper($LocationCode)] += 1;
        }

        /**
         * Determines the common location that this account usually logs into.
         * Returns null if it cannot determine the location
         *
         * @return string
         */
        public function determineCommonLocation(): string
        {
            if(count($this->KnownLocations) == 0)
            {
                return 'None';
            }

            $TopResult = null;

            foreach($this->KnownLocations as $location => $value)
            {
                if($TopResult == null)
                {
                    $TopResult = array(
                        'country' => $location,
                        'value' => $value
                    );
                }
                else
                {
                    if($value > $TopResult['value'])
                    {
                        $TopResult = array(
                            'country' => $location,
                            'value' => $value
                        );
                    }
                }
            }

            if($TopResult == null)
            {
                return 'Unknown';
            }

            return $TopResult['country'];
        }

        /**
         * Returns an array that represents this object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'known_locations' => $this->KnownLocations
            );
        }

        /**
         * Constructs object from array
         *
         * @param array $data
         * @return LoginLocations
         */
        public static function fromArray(array $data): LoginLocations
        {
            $LoginLocationsObject = new LoginLocations();

            if(isset($data['known_locations']))
            {
                $LoginLocationsObject->KnownLocations = $data['known_locations'];
            }

            return $LoginLocationsObject;
        }
    }