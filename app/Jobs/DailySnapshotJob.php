<?php

namespace App\Jobs;

use App\Helpers\Plaid\Client;
use App\Models\Finance\Account;
use App\Models\Finance\Balance;
use App\Models\Finance\Item;
use App\Models\Finance\Transaction;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class DailySnapshotJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $errors = [];
        foreach (Item::all() as $item) {
            try {
                $client = (new Client())->item($item);
                $this->getCurrentBalances($client);
                $this->getRecentTransactions($client);
            } catch (Exception $e) {
                $errors[] = $e->getMessage();
            }
        }

        if (!empty($errors)) {
            dd($errors);
        }
    }

    /**
     * @param \App\Helpers\Plaid\Client $client
     */
    private function getCurrentBalances(Client $client)
    {
        $date = now();
        foreach ($client->balances() as $accountID => $balanceAmount) {
            $account = Account::where('account_id', $accountID)->first();

            if (!$account || $account->isClosed()) {
                continue;
            }

            Balance::updateOrCreate([
                'account_id' => $account->id,
                'date'       => $date->format('Y-m-d'),
            ], ['balance' => $balanceAmount]);

            $account->update(['current_balance' => $balanceAmount]);
        }
    }

    /**
     * @param \App\Helpers\Plaid\Client $client
     */
    private function getRecentTransactions(Client $client)
    {
        $startDate = now()->subDay()->format('Y-m-d');
        $accounts  = collect($client->transactions($startDate))->groupBy('account_id');
        foreach ($accounts as $account_id => $transactions) {
            $account = Account::where('account_id', '=', $account_id)->first();
            if (!$account) {
                continue;
            }

            foreach ($transactions as $transaction) {
                $t = Transaction::updateOrCreate([
                    'transaction_id' => $transaction->transaction_id,
                ], [
                    'account_id'             => $account->id,
                    'name'                   => $transaction->name,
                    'amount'                 => $transaction->amount,
                    'date'                   => $transaction->date,
                    'pending'                => $transaction->pending,
                    'pending_transaction_id' => $transaction->pending_transaction_id,
                ]);

                $t->syncTags((array)$transaction->category);
            }
        }
    }
}
