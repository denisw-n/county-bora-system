<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Report;

class PopulateTrackingNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:populate-tracking-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Populate tracking numbers for existing reports';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $reports = Report::whereNull('tracking_number')->get();
        
        $count = 0;
        foreach ($reports as $report) {
            $report->tracking_number = 'NCC-' . strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
            $report->save();
            $count++;
        }

        $this->info("Successfully updated {$count} reports with tracking numbers.");
    }
}