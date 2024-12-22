<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class AvailableTermCollectionResource extends ResourceCollection
{
    public $collects = AvailableTermResource::class;
}
