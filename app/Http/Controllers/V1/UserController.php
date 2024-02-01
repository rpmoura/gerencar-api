<?php

namespace App\Http\Controllers\V1;

use App\Exceptions\RepositoryException;
use App\Http\Controllers\Controller;
use App\Http\Requests\V1\Users\{CreateUserRequest, EditUserRequest};
use App\Http\Resources\V1\Users\UserResource;
use App\Http\Resources\V1\Users\{UserResourceCollection};
use App\Services\Users\UserService;
use Illuminate\Http\{JsonResponse, Request};
use Illuminate\Support\Facades\Response;
use Symfony\Component\HttpFoundation\Response as HttpStatus;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class UserController extends Controller
{
    public function __construct(private readonly UserService $userService)
    {
        parent::__construct();
    }

    public function post(CreateUserRequest $request): JsonResponse
    {
        $user = $this->userService->create($request->validated());

        $this->response['data']    = ['user' => new UserResource($user)];
        $this->response['message'] = __('message.user.created_successfully');

        return Response::json($this->response, HttpStatus::HTTP_CREATED);
    }

    public function find(Request $request): JsonResponse
    {
        try {
            $user = $this->userService->findOneBy('uuid', $request->route('uuid'));

            $this->response['data']    = ['user' => new UserResource($user)];
            $this->response['message'] = __('message.user.listed_successfully');
        } catch (NotFoundHttpException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_NOT_FOUND);
        }

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }

    public function get(Request $request): JsonResponse
    {
        $perPage = (int)$request->query('per_page', config('pagination.limit'));

        $users = $this->userService->findUsers()->paginate($perPage);

        $this->response['data']    = ['users' => new UserResourceCollection($users)];
        $this->response['message'] = __('message.user.listed_successfully');

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }

    public function put(EditUserRequest $request): JsonResponse
    {
        try {
            $user = $this->userService->update((string)$request->route('uuid'), (array)$request->validated());

            $this->response['data']    = ['user' => new UserResource($user)];
            $this->response['message'] = __('message.user.updated_successfully');
        } catch (NotFoundHttpException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_NOT_FOUND);
        }

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }

    public function delete(Request $request): JsonResponse
    {
        try {
            $this->userService->delete((string)$request->route('uuid'));

            $this->response['message'] = __('message.user.deleted_successfully');
        } catch (NotFoundHttpException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_NOT_FOUND);
        } catch (RepositoryException $exception) {
            return $this->buildResponseError($exception, HttpStatus::HTTP_INTERNAL_SERVER_ERROR);
        }

        return Response::json($this->response, HttpStatus::HTTP_OK);
    }
}
