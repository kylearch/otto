<?php

namespace App\Helpers\Plaid;

use App\Models\Finance\Item;
use Exception;
use stdClass;

class Client
{

    const VERSION = 0.1;

    protected $guzzle;

    protected $item;

    private $credentials = [];

    public function __construct()
    {
        $this->guzzle = new \GuzzleHttp\Client([
            'base_uri' => 'https://' . config('plaid.env') . '.plaid.com',
            'headers'  => [
                'Content-Type'  => 'application/json',
                'User-Agent'    => 'Laravel-Plaid v' . self::VERSION,
                'Plaid-Version' => config('plaid.api_version'),
            ],
        ]);

        $this->item = new Item();

        $this->resetCredentials();
    }

    private function resetCredentials()
    {
        $this->credentials = [
            'client_id' => env('PLAID_CLIENT_ID'),
            'secret'    => env('PLAID_SECRET'),
        ];
    }

    public function item(Item $item)
    {
        $this->item = $item;

        return $this;
    }

    public function setPublicKey(?string $public_key = null)
    {
        $this->credentials['public_key'] = $public_key;
    }

    public function setPublicToken(?string $public_token = null)
    {
        $this->credentials['public_token'] = $public_token;
    }

    public function setAccessToken(?string $access_token = null)
    {
        $this->credentials['access_token'] = $access_token;
    }

    private function request(string $endpoint, array $data = []): stdClass
    {
        $body    = json_encode(array_merge(array_filter($this->credentials), $data));
        $options = [
            'body'    => $body,
            'headers' => [
                'Content-Length' => strlen($body),
            ],
        ];

        try {
            return json_decode($this->guzzle->post($endpoint, $options)->getBody());
        } catch (Exception $e) {
            echo "<pre><code>" . print_r($e->getMessage()) . "</code></pre>";
            return new stdClass();
        }
    }

    public function accessToken(): stdClass
    {
        $this->setPublicToken($this->item->public_token);

        return $this->request('item/public_token/exchange');
    }

    public function balances(): array
    {
        $this->setAccessToken($this->item->access_token);

        $accounts = collect($this->request('accounts/balance/get')->accounts);

        return $accounts->pluck('balances.current', 'account_id')->toArray();
    }

    public function transactions(string $startDate = null, string $endDate = null): array
    {
        $this->resetCredentials();
        $this->setAccessToken($this->item->access_token);

        return $this->request('transactions/get', [
            'start_date' => $startDate ?? now()->subMonth()->format('Y-m-d'),
            'end_date'   => $endDate ?? now()->format('Y-m-d'),
        ])->transactions;
    }

    public function institutionData()
    {
        $this->credentials = [
            'public_key' => env('PLAID_PUBLIC_KEY'),
        ];

        return $this->request('institutions/get_by_id', ['institution_id' => $this->item->institution->institution_id, 'options' => ['include_optional_metadata' => true]])->institution;
    }

}
