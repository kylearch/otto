<?php

namespace App\Services;

use Carbon\Carbon;
use Google_Client;
use Google_Exception;
use Google_Service_Calendar;
use Illuminate\Support\Facades\Log;

class GoogleCalendar
{

    const END_OF_DAY = 18;

    /**
     * @var string
     */
    public static $credentials = 'credentials/google/secret.json';

    /**
     * @var string
     */
    public static $generated = 'credentials/google/generated.json';

    /**
     * @return \Google_Client
     */
    public static function getClient()
    {
        $client = new Google_Client();

        try {
            $client->setApplicationName(config('app.name'));
            $client->setScopes(Google_Service_Calendar::CALENDAR_READONLY);
            $client->setAuthConfig(storage_path(self::$credentials));
            $client->setRedirectUri('http://localhost:8080/google/store');
            $client->setAccessType('offline');
        } catch (Google_Exception $e) {
            Log::error($e->getMessage());
        }

        return $client;
    }

    /**
     * @return bool|\Google_Client
     */
    public static function oauth()
    {
        $client = self::getClient();

        // Load previously authorized credentials from a file.
        $credentialsPath = storage_path(self::$generated);

        if ($client->getAccessToken() !== null) {
            self::setAccessToken($client, $credentialsPath);
        } else if ($client->isAccessTokenExpired() || !file_exists($credentialsPath)) {
            // Refresh the token if it's expired.
            self::saveGenerated($client);
            self::setAccessToken($client, $credentialsPath);
        }

        return $client;
    }

    private static function saveGenerated(Google_Client $client)
    {
        $client->fetchAccessTokenWithRefreshToken(json_encode($client->getRefreshToken()));
        $token = $client->getAccessToken();
        if ($token !== null) {
            file_put_contents(storage_path(self::$generated), json_encode($token));
        }
    }

    /**
     * @param \Google_Client $client
     * @param string         $path
     */
    private static function setAccessToken(Google_Client $client, string $path)
    {
        $accessToken = json_decode(file_get_contents($path), true);
        $client->setAccessToken($accessToken);
    }

    /**
     * @return array|\Illuminate\Support\Collection
     */
    public static function getCurrentEvents()
    {
        $service = new Google_Service_Calendar(self::oauth());
        $return  = collect();

        $calendarId = 'primary';
        $optParams  = [
            'maxResults'   => 10,
            'orderBy'      => 'startTime',
            'singleEvents' => true,
            'timeMin'      => Carbon::now()->startOfMinute()->toIso8601String(),
            // 'timeMax'      => Carbon::now()->hour(self::END_OF_DAY)->startOfHour()->toIso8601String(),
        ];
        $results    = $service->events->listEvents($calendarId, $optParams);

        foreach ($results->getItems() as $event) {
            $return[] = $event->toSimpleObject();
        }

        return $return;
    }
}
