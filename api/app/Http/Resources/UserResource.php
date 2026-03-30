<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Illuminate\Http\Request;

class UserResource extends JsonApiResource
{

    /**
     * Get the resource's attributes.
     */
    public function toAttributes(Request $request): array
    {
        return $this->resource->getAttributes();
    }

    /**
     * Get the resource's relationships.
     */
    public function toRelationships(Request $request): array
    {
        return $this->resource->getRelations();
    }
}
