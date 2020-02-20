<?php

namespace App\Observers;

use App\Http\Models\Devs;
use App\Jobs\SendEmailJob;
use Illuminate\Support\Facades\Log;

class DevObserver
{
    /**
     * Handle the devs "created" event.
     *
     * @param  \App\Http\Models\Devs  $devs
     * @return void
     */
    public function created(Devs $devs)
    {
        Log::alert('created ' . $devs->toJson());

        $details['email'] = 'your_email@gmail.com';
        //dd($devs->toArray());
        SendEmailJob::dispatch($devs->toArray(), $details);
    }

    /**
     * Handle the devs "updated" event.
     *
     * @param  \App\Http\Models\Devs  $devs
     * @return void
     */
    public function updated(Devs $devs)
    {
        //
    }

    /**
     * Handle the devs "deleted" event.
     *
     * @param  \App\Http\Models\Devs  $devs
     * @return void
     */
    public function deleted(Devs $devs)
    {
        //
    }

    /**
     * Handle the devs "restored" event.
     *
     * @param  \App\Http\Models\Devs  $devs
     * @return void
     */
    public function restored(Devs $devs)
    {
        //
    }

    /**
     * Handle the devs "force deleted" event.
     *
     * @param  \App\Http\Models\Devs  $devs
     * @return void
     */
    public function forceDeleted(Devs $devs)
    {
        //
    }
}
