<?php

namespace App\Http\Resources\V1\Users;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'       => $this->uuid,
            'email'      => $this->email,
            'created_at' => $this->created_at->toRfc3339String(),
            'updated_at' => $this->updated_at->toRfc3339String(),
        ];
    }
}
