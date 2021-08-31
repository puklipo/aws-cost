<?php

namespace App\Commands;

use Aws\Laravel\AwsFacade as Aws;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Support\Arr;
use LaravelZero\Framework\Commands\Command;
use Revolution\Line\Facades\LineNotify;
use Revolution\Line\Notifications\LineNotifyMessage;

class AwsCostCommand extends Command
{
    /**
     * The signature of the command.
     *
     * @var string
     */
    protected $signature = 'aws:cost';

    /**
     * The description of the command.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $start = today()->startOfMonth();
        $end = today();

        if ($start->eq($end)) {
            $start = today()->subMonthNoOverflow()->startOfMonth();
            $end = today()->startOfMonth();
        }

        $result = Aws::createCostExplorer()->getCostAndUsage([
            'TimePeriod' => [
                'Start' => $start->toDateString(),
                'End' => $end->toDateString(),
            ],
            'Granularity' => 'MONTHLY',
            'Metrics' => ['AmortizedCost'],
        ]);

        $total = Arr::get($result->toArray(), 'ResultsByTime.0.Total.AmortizedCost.Amount');
        $total = number_format($total, 2);

        $message = "\n${start} ~ ${end}\n${total} USD";

        LineNotify::withToken(config('line.notify.personal_access_token'))
                    ->notify(LineNotifyMessage::create($message)->toArray());
    }

    /**
     * Define the command's schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    public function schedule(Schedule $schedule): void
    {
        // $schedule->command(static::class)->everyMinute();
    }
}
