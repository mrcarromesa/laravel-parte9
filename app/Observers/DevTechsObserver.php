<?php

namespace App\Observers;

use App\Http\Models\Pivot\DevTechs;
use Illuminate\Support\Facades\Log;

class DevTechsObserver
{
    /**
     * Handle the dev techs "created" event.
     *
     * @param  \App\Http\Models\Pivot\DevTechs  $devTechs
     * @return void
     */
    public function created(DevTechs $devTechs)
    {

        Log::alert('pivot ok => ' . $devTechs->toJson());
        //Log::alert('pivot ok');
    }

    /**
     * Handle the dev techs "updated" event.
     *
     * @param  \App\Http\Models\Pivot\DevTechs  $devTechs
     * @return void
     */
    public function updated(DevTechs $devTechs)
    {
        Log::alert('pivot ok UPd => ' . $devTechs->toJson());
        //
    }

    /**
     * Handle the dev techs "deleted" event.
     *
     * @param  \App\Http\Models\Pivot\DevTechs  $devTechs
     * @return void
     */
    public function deleted(DevTechs $devTechs)
    {
        //
    }

    /**
     * Handle the dev techs "restored" event.
     *
     * @param  \App\Http\Models\Pivot\DevTechs  $devTechs
     * @return void
     */
    public function restored(DevTechs $devTechs)
    {
        //
    }

    /**
     * Handle the dev techs "force deleted" event.
     *
     * @param  \App\Http\Models\Pivot\DevTechs  $devTechs
     * @return void
     */
    public function forceDeleted(DevTechs $devTechs)
    {
        //
    }
}
