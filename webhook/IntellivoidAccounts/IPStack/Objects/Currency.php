<?php


    namespace IPStack\Objects;

    /**
     * Class Currency
     * @package IPStack\Objects
     */
    class Currency
    {
        /**
         * Returns the 3-letter code of the main currency associated with the IP.
         * Example: USD
         *
         * @var string
         */
        public $Code;

        /**
         * Returns the name of the given currency.
         *
         * @var string
         */
        public $Name;

        /**
         * 	Returns the plural name of the given currency.
         *
         * @var string
         */
        public $Plural;

        /**
         * Returns the symbol letter of the given currency.
         *
         * @var string
         */
        public $Symbol;

        /**
         * Returns the native symbol letter of the given currency.
         *
         * @var string
         */
        public $SymbolNative;

        /**
         * Creates array from object
         *
         * @return array
         */
        public function toArray(): array
        {
            return array(
                'code' => $this->Code,
                'name' => $this->Name,
                'plural' => $this->Plural,
                'symbol' => $this->Symbol,
                'symbol_native' => $this->SymbolNative
            );
        }

        /**
         * Creates object from array
         *
         * @param array $data
         * @return Currency
         */
        public static function fromArray(array $data): Currency
        {
            $CurrencyObject = new Currency();

            if(isset($data['code']))
            {
                $CurrencyObject->Code = $data['code'];
            }

            if(isset($data['name']))
            {
                $CurrencyObject->Name = $data['name'];
            }

            if(isset($data['plural']))
            {
                $CurrencyObject->Plural = $data['plural'];
            }

            if(isset($data['symbol']))
            {
                $CurrencyObject->Symbol = $data['symbol'];
            }

            if(isset($data['symbol_native']))
            {
                $CurrencyObject->SymbolNative = $data['symbol_native'];
            }

            return $CurrencyObject;
        }
    }