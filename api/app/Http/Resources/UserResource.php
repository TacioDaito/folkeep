<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\JsonApi\JsonApiResource;
use Illuminate\Http\Request;

/**
 * UserResource transforms a User model into a JSON API resource format.
 * It defines how to extract attributes and relationships from the User model for API responses.
 */
class UserResource extends JsonApiResource
{

    /**
     * Get the resource's attributes..
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
