<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class LocationCollectionResource extends ResourceCollection
{
    public $collects = LocationResource::class;
}
