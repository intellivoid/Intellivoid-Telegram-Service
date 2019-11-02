<?php


    namespace IPStack\Abstracts;

    /**
     * Class ThreatType
     * @package IPStack\Abstracts
     */
    abstract class ThreatType
    {
        /**
         * Tor System
         */
        const Tor = 'tor';

        /**
         * Fake Crawler
         */
        const FakeCrawler = 'fake_crawler';

        /**
         * Web Scraper
         */
        const WebScraper = 'web_scraper';

        /**
         * Attack Source identified: HTTP
         */
        const AttackSource = 'attack_source';

        /**
         * Attack Source identified: HTTP
         */
        const AttackSourceHTTP = 'attack_source_http';

        /**
         * Attack Source identified: Mail
         */
        const AttackSourceMail = 'attack_source_mail';

        /**
         * Attack Source identified: SSH
         */
        const AttackSourceSSH = 'attack_source_ssh';
    }