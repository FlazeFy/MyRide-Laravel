<?php

namespace App\Helpers;
use Google_Client;
use Google_Service_Calendar;
use Google_Service_Calendar_Event;
use Google_Service_Calendar_EventDateTime;
use Carbon\Carbon;

class GoogleCalendar
{
    public static function createClient($accessToken)
    {
        $client = new Google_Client();
        $client->setAccessToken($accessToken);
        $client->addScope(Google_Service_Calendar::CALENDAR);
        return new Google_Service_Calendar($client);
    }

    public static function createSingleEvent($accessToken, $summary, $start, $durationMinutes = 60)
    {
        $calendarService = self::createClient($accessToken);

        // Convert to RFC3339
        $startDate = Carbon::parse($start)->toRfc3339String();
        $endDate = Carbon::parse($start)->addMinutes($durationMinutes)->toRfc3339String();

        $event = new Google_Service_Calendar_Event([
            'summary' => $summary,
            'start' => [
                'dateTime' => $startDate,
                'timeZone' => 'Asia/Jakarta',
            ],
            'end' => [
                'dateTime' => $endDate,
                'timeZone' => 'Asia/Jakarta',
            ],
        ]);

        return $calendarService->events->insert('primary', $event);
    }
}
