<?php

    use acm\acm;
    use acm\Objects\Schema;

    /**
     * ACM AutoConfig file for Intellivoid Accounts 2.0 (v2.0.1.1/v2.0.1.3)
     *
     * !! UPDATE OLD CONFIGURATION FILES BEFORE PRODUCTION !!
     */

    if(class_exists('acm\acm') == false)
    {
        include_once(__DIR__ . DIRECTORY_SEPARATOR . 'acm' . DIRECTORY_SEPARATOR . 'acm.php');
    }

    $acm = new acm(__DIR__, 'Intellivoid Accounts');

    // Database Schema Configuration
    $DatabaseSchema = new Schema();
    $DatabaseSchema->setDefinition('Host', 'localhost');
    $DatabaseSchema->setDefinition('Port', '3306');
    $DatabaseSchema->setDefinition('Username', 'root');
    $DatabaseSchema->setDefinition('Password', '');
    $DatabaseSchema->setDefinition('Name', 'intellivoid');
    $acm->defineSchema('Database', $DatabaseSchema);

    // IPStack Schema Configuration (For GeoLocating IP Addresses)
    $IpStackSchema = new Schema();
    $IpStackSchema->setDefinition('AccessKey', '<API KEY>');
    $IpStackSchema->setDefinition('UseSSL', 'false');
    $IpStackSchema->setDefinition('IpStackHost', 'api.ipstack.com');
    $acm->defineSchema('IpStack', $IpStackSchema);

    // Profile Picture Schema Location for both User and Application media
    $SystemSchema = new Schema();
    $SystemSchema->setDefinition('ProfilesLocation_Unix', '/etc/user_pictures');
    $SystemSchema->setDefinition('ProfilesLocation_Windows', 'C:\\user_pictures');
    $SystemSchema->setDefinition('AppIconsLocation_Unix', '/etc/app_icons');
    $SystemSchema->setDefinition('AppIconsLocation_Windows', 'C:\\app_icons');
    $acm->defineSchema('System', $SystemSchema);

    // Telegram Service Schema
    $TelegramSchema = new Schema();
    $TelegramSchema->setDefinition('TgBotName', 'IntellivoidBot');
    $TelegramSchema->setDefinition('TgBotToken', '<BOT TOKEN>');
    $TelegramSchema->setDefinition('TgBotEnabled', 'true');
    $TelegramSchema->setDefinition('TgBotHook', 'http://localhost');
    $acm->defineSchema('TelegramService', $TelegramSchema);

    // If auto-loaded via CLI, Process any arguments passed to the main execution point
    $acm->processCommandLine();