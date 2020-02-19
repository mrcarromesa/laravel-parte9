<?php

namespace App\Http\Models\Pivot;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\Pivot;

class DevTechs extends Pivot
{
    public function create()
    {
        event('eloquent.creating: ' . __CLASS__, $this);

        parent::create();

        event('eloquent.created: ' . __CLASS__, $this);
    }

    public function update(array $attributes = [], array $options = [])
    {
        event('eloquent.updating: ' . __CLASS__, $this);

        parent::update();

        event('eloquent.updated: ' . __CLASS__, $this);
    }

    public function delete()
    {
        event('eloquent.deleting: ' . __CLASS__, $this);

        parent::delete();

        event('eloquent.deleted: ' . __CLASS__, $this);
    }
}
