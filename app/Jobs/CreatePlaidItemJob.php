<?php

namespace App\Jobs;

use App\Helpers\Plaid\Client;
use App\Models\Finance\Institution;
use App\Models\Finance\Item;
use ColorThief\ColorThief;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Http\Request;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Storage;

class CreatePlaidItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * @var array
     */
    private $input;

    /**
     * @var \Illuminate\Support\Facades\Request
     */
    private $request;

    /**
     * @var \App\Helpers\Plaid\Client
     */
    private $client;

    /**
     * @var \App\Models\Finance\Institution
     */
    private $institution;

    /**
     * @var Item
     */
    private $item;

    /**
     * Create a new job instance.
     *
     * @param array $request
     */
    public function __construct(array $input)
    {
        $this->input = $input;
        Log::debug($input);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            $this->init();

            $this->saveInstitution();

            $this->makeItem();

            $this->storeItem();

            $this->saveInstitutionLogo();

            $this->saveAccounts();

            $this->getRecentTransactions();

            $this->saveCurrentBalances();
        } catch (\Exception $e) {
            dd($e->getMessage());
        }
    }

    /**
     *
     */
    private function init()
    {
        $this->request = new Request();
        $this->request->replace($this->input);
        unset($this->input);

        $this->client = new Client();
    }

    /**
     * @return void
     */
    private function saveInstitution()
    {
        $this->institution = Institution::firstOrCreate([
            'name'           => $this->request->input('institution.name'),
            'institution_id' => $this->request->input('institution.institution_id'),
        ]);
    }

    /**
     * @return void
     */
    private function saveInstitutionLogo()
    {
        if ($this->institution->wasRecentlyCreated) {
            $institution_data = $this->client->item($this->item)->institutionData();
            $file_name        = "public/img/institutions/logos/{$this->institution->institution_id}_round.png";
            Storage::put($file_name, base64_decode($institution_data->logo));

            $this->saveSquareLogo();
        }
    }

    /**
     * @return void
     */
    private function saveSquareLogo()
    {
        $filePath = storage_path("app/public/img/institutions/logos/{$this->institution->institution_id}_round.png");
        $savePath = str_replace('_round.png', '.png', $filePath);

        $palette  = ColorThief::getPalette($filePath, 2);
        $dominant = array_shift($palette);

        $img    = @imagecreatefrompng($filePath);
        $width  = imagesx($img);
        $height = imagesy($img);

        $backgroundImg = @imagecreatetruecolor($width, $height);
        $color         = imagecolorallocate($backgroundImg, $dominant[0], $dominant[1], $dominant[2]);

        imagefill($backgroundImg, 0, 0, $color);
        imagecopy($backgroundImg, $img, 0, 0, 0, 0, $width, $height);
        imagepng($backgroundImg, $savePath, 0);
    }

    /**
     * @return void
     */
    private function makeItem()
    {
        $this->item = new Item([
            'name'         => $this->request->input('institution.name'),
            'public_token' => $this->request->input('public_token'),
        ]);

        $this->client->item($this->item);
    }

    /**
     * @return void
     */
    private function storeItem()
    {
        $response = $this->client->accessToken();

        $this->item = $this->institution->items()->updateOrCreate([
            'item_id' => $response->item_id,
        ], [
            'name'         => $this->item->name,
            'public_token' => $this->item->public_token,
            'access_token' => $response->access_token,
        ]);
    }

    /**
     * @return void
     */
    private function saveAccounts()
    {
        foreach ((array)$this->request->accounts as $account) {
            $this->item->accounts()->updateOrCreate([
                'account_id' => $account['id'],
            ], [
                'institution_id' => $this->institution->institution_id,
                'name'           => $account['name'],
                'mask'           => (int)$account['mask'],
                'type'           => $account['type'],
                'subtype'        => $account['subtype'],
            ]);
        }
    }

    /**
     * @return void
     */
    private function getRecentTransactions()
    {
        $this->client->item($this->item);

        $accounts = collect($this->client->transactions())->groupBy('account_id');
        foreach ($accounts as $account_id => $transactions) {
            $account = $this->item->accounts->firstWhere('account_id', '=', $account_id);
            if (!$account) {
                continue;
            }

            foreach ($transactions as $transaction) {
                $t = $account->transactions()->updateOrCreate([
                    'transaction_id' => $transaction->transaction_id,
                ], [
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

    /**
     * @return void
     */
    public function saveCurrentBalances()
    {
        $date = now()->format('Y-m-d');
        foreach ($this->client->balances() as $account_id => $balanceAmount) {
            $account = $this->item->accounts->firstWhere('account_id', $account_id);
            $account->balances()->updateOrCreate([
                'account_id' => $account_id,
                'date'       => $date,
            ], ['balance' => $balanceAmount]);

            $account->update(['current_balance' => $balanceAmount]);
        }
    }

}
