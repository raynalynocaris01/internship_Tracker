<?php

namespace App\Traits;

use Carbon\Carbon;

trait HasTimeTracking
{
    public function calculateTimeDifference($startTime, $endTime)
    {
        $start = Carbon::parse($startTime);
        $end = Carbon::parse($endTime);
        
        return $end->diffInSeconds($start) / 3600;
    }

    public function formatTime($time)
    {
        if (!$time) return null;
        return Carbon::parse($time)->format('h:i A');
    }

    public function isWithinTimeRange($time, $start, $end)
    {
        $checkTime = Carbon::parse($time);
        $startTime = Carbon::parse($start);
        $endTime = Carbon::parse($end);
        
        return $checkTime->between($startTime, $endTime);
    }
}