<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\ResourceCollection;

class VisitCollectionResource extends ResourceCollection
{
    public $collects = VisitResource::class;
}
