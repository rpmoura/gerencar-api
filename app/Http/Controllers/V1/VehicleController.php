<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Vehicles\{CreateVehicleRequest, EditVehicleRequest};
use App\Http\Resources\V1\Vehicles\{VehicleResource, VehicleResourceCollection};
use App\Services\Contracts\VehicleServiceInterface;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpStatus;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class VehicleController extends Controller
{
    public function __construct(private readonly VehicleServiceInterface $vehicleService)
    {
        parent::__construct();
    }

    public function post(CreateVehicleRequest $request): JsonResponse
    {
        $vehicle = $this->vehicleService->create($request->validated());

        $this->response['data']    = ['vehicle' => new VehicleResource($vehicle)];
        $this->response['message'] = __('message.vehicle.created_successfully');

        return Response::json($this->response, HttpStatus::HTTP_CREATED);
    }

    public function find(Request $request): JsonResponse
    {
        try {
            $vehicle = $this->vehicleService->findOneBy('uuid', $request->route('uuid'));

            $this->response['data']    = ['vehicle' => new VehicleResource($vehicle)];
            $this->response['message'] = __('message.vehicle.listed_successfully');
        } catch (NotFoundHttpException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_NOT_FOUND);
        }

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }

    public function get(Request $request): JsonResponse
    {
        $perPage = (int)$request->query('per_page', config('pagination.limit'));

        $vehicles = $this->vehicleService->findVehicles()->paginate($perPage);

        $this->response['data']    = ['vehicles' => new VehicleResourceCollection($vehicles)];
        $this->response['message'] = __('message.vehicle.listed_successfully');

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }

    public function put(EditVehicleRequest $request): JsonResponse
    {
        try {
            $vehicle = $this->vehicleService->update((string)$request->route('uuid'), (array)$request->validated());

            $this->response['data']    = ['vehicle' => new VehicleResource($vehicle)];
            $this->response['message'] = __('message.vehicle.updated_successfully');
        } catch (NotFoundHttpException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_NOT_FOUND);
        }

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->vehicleService->delete((string)$request->route('uuid'));

            $this->response['message'] = __('message.vehicle.deleted_successfully');
        } catch (NotFoundHttpException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_NOT_FOUND);
        } catch (RepositoryException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }
}
