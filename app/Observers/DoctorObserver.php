<?php

namespace App\Observers;

use App\Models\Doctor;
use Illuminate\Support\Facades\Storage;
use App\Events\CustomerHeadquarterUpdated;

class DoctorObserver
{
    public function updated(Doctor $doctor)
    {
        if ($doctor->isDirty('profile_photo')) {
            $this->deletePhoto($doctor->getOriginal('profile_photo'));
        }
        if ($doctor->isDirty('headquarter_id')) {
            event(new CustomerHeadquarterUpdated($doctor, $doctor->headquarter_id));
        }
    }


    public function deleted(Doctor $doctor)
    {
        if ($doctor->profile_photo) {
            $this->deletePhoto($doctor->profile_photo);
        }
    }

    public function deleting(Doctor $doctor)
    {
        $doctor->products()->detach();
        $doctor->tags()->detach();
    }

    public function deletePhoto(?string $path): void
    {
        if ($path) {
            Storage::disk('s3')->delete($path);
        }
    }
}
