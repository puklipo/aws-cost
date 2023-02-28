<?php

namespace App\Commands;

use Aws\Sdk;
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
     */
    public function handle(): int
    {
        $start = today()->startOfMonth()->toDateString();
        $end = today()->toDateString();

        if ($start === $end) {
            $start = today()->subMonthNoOverflow()->startOfMonth()->toDateString();
            $end = today()->subMonthNoOverflow()->endOfMonth()->toDateString();
        }

        $aws = new Sdk(config('services.aws'));

        $result = $aws->createCostExplorer()->getCostAndUsage([
            'TimePeriod' => [
                'Start' => $start,
                'End' => $end,
            ],
            'Granularity' => 'MONTHLY',
            'Metrics' => ['AmortizedCost'],
        ]);

        $total = Arr::get($result->toArray(), 'ResultsByTime.0.Total.AmortizedCost.Amount');
        $total = number_format($total, 2);

        $message = collect([
            PHP_EOL,
            $start,
            ' ~ ',
            $end,
            PHP_EOL,
            $total,
            ' USD',
        ])->join('');

        LineNotify::withToken(config('line.notify.personal_access_token'))
                  ->notify(LineNotifyMessage::create($message)->toArray());

        return 0;
    }
}
