<?php

namespace App\Jobs;

use App\Models\CertificateApplication;
use App\Models\User;
use App\Notifications\Academy\AcademyApplicationNotification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendAcademyApplicationMailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;

    /**
     * @param  class-string<AcademyApplicationNotification>  $notificationClass
     */
    public function __construct(
        public int $userId,
        public int $applicationId,
        public string $notificationClass,
    ) {
        $this->onQueue('mail');
    }

    public function handle(): void
    {
        if (! is_subclass_of($this->notificationClass, AcademyApplicationNotification::class)) {
            return;
        }

        $user = User::query()->find($this->userId);
        $application = CertificateApplication::query()->find($this->applicationId);

        if (! $user || ! $application) {
            return;
        }

        $application->loadMissing(['course', 'certificate']);

        /** @var AcademyApplicationNotification $notification */
        $notification = new $this->notificationClass($application);

        $user->notifyNow($notification, ['mail']);
    }
}
