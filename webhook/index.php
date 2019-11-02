<?php

    use IntellivoidAccounts\IntellivoidAccounts;
    use Longman\TelegramBot\Exception\TelegramException;

    define("__INTELLIVOID_ACCOUNTS__", __DIR__ . DIRECTORY_SEPARATOR . 'IntellivoidAccounts');
    define("__BOT_SCRIPTS__", __DIR__ . DIRECTORY_SEPARATOR . 'scripts');

    require_once(__DIR__ .  DIRECTORY_SEPARATOR . 'vendor' . DIRECTORY_SEPARATOR .  'autoload.php');
    require_once(__INTELLIVOID_ACCOUNTS__ . DIRECTORY_SEPARATOR . 'IntellivoidAccounts.php');

    $IntellivoidAccounts = new IntellivoidAccounts();
    $TelegramConfiguration = $IntellivoidAccounts->getTelegramConfiguration();

    define("__BOT_NAME__", $TelegramConfiguration['TgBotName']);
    define("__BOT_TOKEN__", $TelegramConfiguration['TgBotToken']);
    define("__BOT_HOOK__", $TelegramConfiguration['TgBotHook']);

    if(strtolower($TelegramConfiguration['TgBotEnabled']) == 'true')
    {
        define("__BOT_ENABLED__", true);
    }
    else
    {
        define("__BOT_ENABLED__", false);
    }

    // Update Webhook
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
        $telegram = new Longman\TelegramBot\Telegram(__BOT_TOKEN__, __BOT_NAME__);
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
            $result = $telegram->setWebhook(__BOT_HOOK__);
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