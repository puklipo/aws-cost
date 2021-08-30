<?php

namespace App\Commands;

use Illuminate\Console\Scheduling\Schedule;
use LaravelZero\Framework\Commands\Command;
use Aws\Laravel\AwsFacade as Aws;
use Illuminate\Support\Arr;
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
        $client = Aws::createCostExplorer();

        $start = today()->startOfMonth()->toDateString();
        $end = today()->toDateString();

        if ($start === $end) {
            $start = today()->subMonth()->startOfMonth()->toDateString();
            $end = today()->startOfMonth()->toDateString();
        }

        $result = $client->getCostAndUsage([
            'TimePeriod' => [
                'Start' => $start,
                'End' => $end
            ],
            'Granularity' => 'MONTHLY',
            'Metrics' => ['AmortizedCost'],
        ]);

        $total = Arr::get($result->toArray(),'ResultsByTime.0.Total.AmortizedCost.Amount');
        $total = number_format($total, 2);

        $message = LineNotifyMessage::create(PHP_EOL."$start ~ $end".PHP_EOL.$total.' USD');

        LineNotify::withToken(config('line.notify.personal_access_token'))->notify($message->toArray());
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
