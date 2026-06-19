<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use App\Models\Requisition;
use App\Models\Notificaton;

class SendDeliveryAlerts extends Command
{
    protected $signature = 'alerts:delivery';
    protected $description = 'Create notifications 7 days before a requisition delivery_date';

    public function handle(): int
    {
        // Run for dates exactly 7 days from "today" (app timezone)
        $targetDate = Carbon::today()->addDays(7)->toDateString();

        $requisitions = Requisition::query()
            ->whereDate('delivery_date', $targetDate)
            ->get(['id', 'package_no', 'delivery_date']);

        $created = 0;

        foreach ($requisitions as $r) {
            $pkgNo = $r->package_no ?: 'N/A';
            $deliv = Carbon::parse($r->delivery_date)->format('Y-m-d');

            $text = "Reminder: Package No {$pkgNo} requisition delivery date is on {$deliv} (7 days from today).";

            // Avoid duplicates if scheduler runs multiple times today
            $already = Notificaton::where('text', $text)
                ->whereDate('created_at', Carbon::today())
                ->exists();

            if (!$already) {
                Notificaton::create([
                    'text'    => $text,
                    'is_seen' => false,
                ]);
                $created++;
            }
        }

        $this->info("Delivery alerts created: {$created}");
        return Command::SUCCESS;
    }
}
