<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Minishlink\WebPush\VAPID;

class GenerateWebPushVapidKeys extends Command
{
    protected $signature = 'webpush:vapid';

    protected $description = 'Generate VAPID keys for Web Push (add to .env as WEBPUSH_PUBLIC_KEY / WEBPUSH_PRIVATE_KEY)';

    public function handle(): int
    {
        $keys = VAPID::createVapidKeys();

        $this->line('WEBPUSH_PUBLIC_KEY='.$keys['publicKey']);
        $this->line('WEBPUSH_PRIVATE_KEY='.$keys['privateKey']);
        $this->newLine();
        $this->info('Add these to backend/.env and restart the app container.');

        return self::SUCCESS;
    }
}
