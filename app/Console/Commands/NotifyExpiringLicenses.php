<?php

namespace App\Console\Commands;

use App\Models\License;
use App\Notifications\LicenseExpiringNotification;
use Illuminate\Console\Command;

class NotifyExpiringLicenses extends Command
{
    protected $signature = 'licenses:notify-expiring
                            {--days=7 : Notify users whose license expires in this many days}';

    protected $description = 'Send email notifications to users whose license expires in the given number of days';

    public function handle(): int
    {
        $days = (int) $this->option('days');

        $licenses = License::with('user')
            ->where('is_active', true)
            ->whereDate('expires_at', now()->addDays($days)->toDateString())
            ->get();

        foreach ($licenses as $license) {
            $license->user->notify(new LicenseExpiringNotification($license));
        }

        $this->info("Notified {$licenses->count()} user(s) about licenses expiring in {$days} day(s).");

        return self::SUCCESS;
    }
}
