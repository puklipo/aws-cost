<?php

namespace App\Console\Commands;

use Aws\Sdk;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;
use Illuminate\Support\Number;
use Revolution\Line\Facades\LineNotify;
use Revolution\Line\Notifications\LineNotifyMessage;

class AwsCostCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'aws:cost';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle(Sdk $aws): int
    {
        [$start, $end] = $this->date();

        $result = $aws->createCostExplorer()->getCostAndUsage([
            'TimePeriod' => [
                'Start' => $start,
                'End' => $end,
            ],
            'Granularity' => 'MONTHLY',
            'Metrics' => ['AmortizedCost'],
        ]);

        $total = Arr::get($result->toArray(), 'ResultsByTime.0.Total.AmortizedCost.Amount');
        $total = Number::format($total, precision: 2);

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

    /**
     * @return array<string>
     */
    private function date(): array
    {
        $start = today()->startOfMonth()->toDateString();
        $end = today()->toDateString();

        //毎月1日は先月分のデータを取得
        if ($start === $end) {
            $start = today()->subMonthNoOverflow()->startOfMonth()->toDateString();
            $end = today()->subMonthNoOverflow()->endOfMonth()->toDateString();
        }

        return [$start, $end];
    }
}
