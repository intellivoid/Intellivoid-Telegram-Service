<?php


    namespace IPStack\Abstracts;

    /**
     * Class ThreatLevel
     * @package IPStack\Abstracts
     */
    abstract class ThreatLevel
    {
        /**
         * Low Risk
         */
        const Low = 'low';

        /**
         * Medium Risk
         */
        const Medium = 'medium';

        /**
         * High Risk
         */
        const High = 'high';

        const Unknown = 'unknown';
    }