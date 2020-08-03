<?php

namespace App\Listeners;

use App\Helpers\SettingsHelper;
use App\Notifications\SpeedtestFailedSlack;
use App\Notifications\SpeedtestFailedTelegram;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;
use NotificationChannels\Telegram\TelegramChannel;

class SpeedtestFailedListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        if(SettingsHelper::get('slack_webhook')->value == true) {
            try {
                Notification::route('slack', SettingsHelper::get('slack_webhook')->value)
                            ->notify(new SpeedtestFailedSlack());
            } catch(Exception $e) {
                Log::notice('Your sleck webhook is invalid');
                Log::notice($e);
            }
        }

        if(SettingsHelper::get('telegram_bot_token')->value == true && SettingsHelper::get('telegram_chat_id')->value == true) {
            try {
                config([ 'services.telegram-bot-api' => [ 'token' => SettingsHelper::get('telegram_bot_token')->value ] ]);
                Notification::route(TelegramChannel::class, SettingsHelper::get('telegram_chat_id')->value)
                            ->notify(new SpeedtestFailedTelegram());
            } catch(Exception $e) {
                Log::notice('Your telegram settings are invalid');
                Log::notice($e);
            }
        }
    }
}
