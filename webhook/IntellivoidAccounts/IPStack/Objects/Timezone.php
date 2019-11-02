<?php


    namespace IPStack\Objects;

    /**
     * Class Timezone
     * @package IPStack\Objects
     */
    class Timezone
    {
        /**
         * 	Returns the ID of the time zone associated with the IP. (e.g. America/Los_Angeles for PST)
         *
         * @var string
         */
        public $ID;

        /***
         * Returns the current date and time in the location associated with the IP. (e.g. 2018-03-29T22:31:27-07:00)
         *
         * @var string
         */
        public $CurrentName;

        /**
         * Returns the GMT offset of the given time zone in seconds. (e.g. -25200 for PST's -7h GMT offset)
         *
         * @var int
         */
        public $GMT_Offset;

        /**
         * 	Returns the universal code of the given time zone.
         *
         * @var string
         */
        public $Code;

        /**
         * 	Returns true or false depending on whether or not the given time zone is considered daylight saving time.
         *
         * @var bool
         */
        public $IsDaylightSaving;

        /**
         * Creates array from object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'id' => $this->ID,
                'current_time' => $this->CurrentName,
                'gmt_offset' => $this->GMT_Offset,
                'code' => $this->Code,
                'is_daylight_saving' => $this->IsDaylightSaving
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return Timezone
         */
        public static function fromArray(array $data): Timezone
        {
            $TimezoneObject = new Timezone();

            if(isset($data['id']))
            {
                $TimezoneObject = $data['id'];
            }

            if(isset($data['current_time']))
            {
                $TimezoneObject = $data['current_time'];
            }

            if(isset($data['gmt_offset']))
            {
                $TimezoneObject = $data['gmt_offset'];
            }

            if(isset($data['code']))
            {
                $TimezoneObject = $data['code'];
            }

            if(isset($data['is_daylight_saving']))
            {
                $TimezoneObject = $data['is_daylight_saving'];
            }

            return $TimezoneObject;
        }
    }