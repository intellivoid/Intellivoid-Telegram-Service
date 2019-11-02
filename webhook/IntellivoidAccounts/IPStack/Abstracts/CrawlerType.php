<?php


    namespace IPStack\Abstracts;

    /**
     * Class CrawlerType
     * @package IPStack\Abstracts
     */
    abstract class CrawlerType
    {
        /**
         * Unrecognized
         */
        const Unrecognized = 'unrecognized';

        /**
         * Search Engine Bot
         */
        const SearchEngineBot = 'search_engine_bot';

        /**
         * Site Monitor
         */
        const SiteMonitor = 'site_monitor';

        /**
         * Screenshot Creator
         */
        const ScreenshotCreator = 'screenshot_creator';

        /**
         * Link Checker
         */
        const LinkChecker = 'link_checker';

        /**
         * Wearable Computer
         */
        const WearableComputer = 'wearable_computer';

        /**
         * Web Scraper
         */
        const WebScraper = 'web_scraper';

        /**
         * Vulnerability Scanner
         */
        const VulnerabilityScanner = 'vulnerability_scanner';

        /**
         * Virus Scanner
         */
        const VirusScanner = 'virus_scanner';

        /**
         * Speed Tester
         */
        const SpeedTester = 'speed_tester';

        /**
         * Tool
         */
        const Tool = 'tool';

        /**
         * Marketing
         */
        const Marketing = 'marketeing';
    }