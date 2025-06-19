<?php

namespace App\Services;

use App\Models\Doctor;
use Hash;

class DoctorService
{
    public function create(array $data)
    {
        $data['password'] = Hash::make($data['password']);
        $data['reviews'] = 0 ;
        $data['photo'] = "" ;
        
        return Doctor::create($data);
    }
}
