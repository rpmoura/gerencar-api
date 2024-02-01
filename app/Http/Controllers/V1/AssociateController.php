<?php

namespace App\Http\Controllers\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\V1\Vehicles\VehicleResourceCollection;
use App\Services\Contracts\AssociateServiceInterface;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpStatus;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class AssociateController extends Controller
{
    public function __construct(
        private readonly AssociateServiceInterface $associateService
    ) {
    }

    public function post(Request $request): JsonResponse
    {
        try {
            $user    = $request->route('user');
            $vehicle = $request->route('vehicle');

            $this->associateService->create($user, $vehicle);

            $this->response['message'] = __('message.user.vehicle.associate_successfully');
        } catch (NotFoundHttpException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_NOT_FOUND);
        }

        return Response::json($this->response, HttpStatus::HTTP_CREATED);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $user    = $request->route('user');
            $vehicle = $request->route('vehicle');

            $this->associateService->delete($user, $vehicle);

            $this->response['message'] = __('message.user.vehicle.disassociate_successfully');
        } catch (NotFoundHttpException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_NOT_FOUND);
        }

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }

    public function get(Request $request): JsonResponse
    {
        try {
            $perPage = (int)$request->query('per_page', config('pagination.limit'));

            $user = $request->route('user');

            $vehicles = $this->associateService->listUserVehicles($user)->paginate($perPage);

            $this->response['message'] = __('message.user.vehicle.listed_successfully');
            $this->response['data']    = ['vehicles' => new VehicleResourceCollection($vehicles)];
        } catch (NotFoundHttpException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_NOT_FOUND);
        }

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }
}
