<?php

    use acm\acm;
use Longman\TelegramBot\Exception\TelegramException;

require_once __DIR__ .  DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .  'autoload.php';
    include_once(__DIR__ . DIRECTORY_SEPARATOR . 'AutoConfig.php');

    $acm = new acm(__DIR__, 'Intellivoid Telegram Service');
    $telegram_config = $acm->getConfiguration('Telegram');

    $set_webhook = false;
    if(isset($_GET['set_webhook']))
    {
        if($_GET['set_webhook'] == '1')
        {
            $set_webhook = true;
        }
    }

    try
    {
        $telegram = new Longman\TelegramBot\Telegram($telegram_config['BotToken'], $telegram_config['BotName']);
        $telegram->addCommandsPaths([__DIR__ . DIRECTORY_SEPARATOR . 'commands']);
    }
    catch (Longman\TelegramBot\Exception\TelegramException $e)
    {
        echo $e->getMessage();
    }

    if($set_webhook == true)
    {
        try
        {
            $result = $telegram->setWebhook($telegram_config['BotWebhookUrl']);
            if ($result->isOk())
            {
                echo $result->getDescription();
            }
        }
        catch (Longman\TelegramBot\Exception\TelegramException $e)
        {
            echo $e->getMessage();
        }
    }
    else
    {
        // Add commands paths containing your custom commands
        try
        {
            $telegram->handle();
        }
        catch (TelegramException $e)
        {
            echo $e->getMessage();
        }
    }