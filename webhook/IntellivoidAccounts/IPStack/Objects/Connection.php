<?php


    namespace IPStack\Objects;

    /**
     * Class Connection
     * @package IPStack\Objects
     */
    class Connection
    {
        /**
         * 	Returns the Autonomous System Number associated with the IP.
         *
         * @var string
         */
        public $ASN;

        /**
         * 	Returns the name of the ISP associated with the IP.
         *
         * @var string
         */
        public $ISP;

        /**
         * Creates array from object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'asn' => $this->ASN,
                'isp' => $this->ISP
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return Connection
         */
        public static function fromArray(array $data): Connection
        {
            $ConnectionObject = new Connection();

            if(isset($data['asn']))
            {
                $ConnectionObject->ASN = $data['asn'];
            }

            if(isset($data['isp']))
            {
                $ConnectionObject->ISP = $data['isp'];
            }

            return $ConnectionObject;
        }
    }