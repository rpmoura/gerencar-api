<?php

namespace App\Http\Resources;

use Illuminate\Contracts\Support\Arrayable;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

abstract class ResourceCollectionAbstract extends ResourceCollection
{
    private ?array $links = null;

    private ?array $meta = null;

    public function __construct($resource)
    {
        if ($resource instanceof LengthAwarePaginator) {
            $this->meta = [
                'pages'        => $resource->lastPage(),
                'count'        => $resource->count(),
                'per_page'     => $resource->perPage(),
                'current_page' => $resource->currentPage(),
                'first'        => $resource->firstItem(),
                'last'         => $resource->lastItem(),
                'total'        => $resource->total(),
            ];

            $this->links = [
                'first_page' => $resource->url(1),
                'last_page'  => $resource->url($resource->lastPage()),
                'previous'   => $resource->previousPageUrl(),
                'next'       => $resource->nextPageUrl(),
            ];

            $resource = $resource->getCollection();
        }

        parent::__construct($resource);
    }

    public function toArray($request): array|Collection|\JsonSerializable|Arrayable
    {
        if (is_null($this->meta) || is_null($this->links)) {
            return $this->collection;
        }

        return [
            'data'  => $this->collection,
            'meta'  => $this->meta,
            'links' => $this->links,
        ];
    }
}
