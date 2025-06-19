<?php

namespace App\Services;


use App\Models\WeeklySchedule;

class WeeklyScheduleService
{
    public function create(array $data)
    {
        return WeeklySchedule::create($data);
    }
}
