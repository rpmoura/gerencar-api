<?php

namespace App\Http\Resources\V1\Vehicles;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class VehicleResource extends JsonResource
{
    /**
     * @param Request $request
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'uuid'       => $this->uuid,
            'brand'      => $this->brand,
            'model'      => $this->model,
            'created_at' => $this->created_at->toRfc3339String(),
            'updated_at' => $this->updated_at->toRfc3339String(),
        ];
    }
}
