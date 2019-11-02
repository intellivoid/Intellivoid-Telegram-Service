<?php


    namespace IPStack\Abstracts;

    /**
     * Class ProxyType
     * @package IPStack\Abstracts
     */
    abstract class ProxyType
    {
        /**
         * CGI Proxy
         */
        const CGI = 'cgi';

        /**
         * Web Proxy
         */
        const Web = 'web';

        /**
         * VPN Proxy
         */
        const VPN = 'vpn';
    }