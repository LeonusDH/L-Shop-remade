<?php
declare(strict_types = 1);

namespace app\Http\Controllers\Admin\Users;

use app\DataTransferObjects\PaginationList;
use app\Exceptions\Permission\PermissionNotFoundException;
use app\Exceptions\Role\RoleAlreadyExistsException;
use app\Exceptions\Role\RoleNotFoundException;
use app\Handlers\Admin\Users\Roles\CreateHandler;
use app\Handlers\Admin\Users\Roles\DeleteHandler;
use app\Handlers\Admin\Users\Roles\PaginationHandler;
use app\Handlers\Admin\Users\Roles\RolePermissionsHandler;
use app\Handlers\Admin\Users\Roles\UpdateNameHandler;
use app\Handlers\Admin\Users\Roles\UpdatePermissionsHandler;
use app\Http\Controllers\Controller;
use app\Http\Requests\Admin\Users\Roles\CreateUpdateRequest;
use app\Http\Requests\Admin\Users\Roles\UpdateNameRequest;
use app\Services\Auth\Permissions;
use app\Services\Notification\Notifications\Error;
use app\Services\Notification\Notifications\Info;
use app\Services\Notification\Notifications\Success;
use app\Services\Response\JsonResponse;
use app\Services\Response\Status;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use function app\permission_middleware;

class RolesController extends Controller
{
    public function __construct()
    {
        $this->middleware(permission_middleware(Permissions::ADMIN_ROLES_CRUD_ACCESS));
    }

    public function pagination(Request $request, PaginationHandler $handler): JsonResponse
    {
        $dto = $handler->handle(
            (new PaginationList())
                ->setOrderBy($request->get('order_by') ?? 'role.id')
                ->setDescending((bool)$request->get('descending'))
                ->setSearch($request->get('search'))
                ->setPage((int)($request->get('page') ?? 1))
                ->setPerPage((int)($request->get('per_page') ?? 25))
        );

        return new JsonResponse(Status::SUCCESS, $dto);
    }

    public function create(CreateUpdateRequest $request, CreateHandler $handler): JsonResponse
    {
        try {
            $handler->handle($request->get('name'), $request->get('permissions'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.users.roles.successfully_created')));
        } catch (PermissionNotFoundException $e) {
            return (new JsonResponse('permission_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.users.roles.permission_not_found_with_name', ['name' => $e->getCause()])));
        } catch (RoleAlreadyExistsException $e) {
            return (new JsonResponse('role_already_exists'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Error(__('msg.admin.users.roles.already_exists_with_name', ['name' => $e->getCause()])));
        }
    }

    public function rolePermissions(Request $request, RolePermissionsHandler $handler): JsonResponse
    {
        try {
            $permissions = $handler->handle((int)$request->route('role'));

            return new JsonResponse(Status::SUCCESS, [
                'permissions' => $permissions
            ]);
        } catch (RoleNotFoundException $e) {
            return (new JsonResponse('role_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.users.roles.not_found')));
        }
    }

    public function updateName(UpdateNameRequest $request, UpdateNameHandler $handler): JsonResponse
    {
        try {
            $handler->handle((int)$request->route('role'), $request->get('name'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.users.roles.successfully_updated')));
        } catch (RoleNotFoundException $e) {
            return (new JsonResponse('not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.users.roles.not_found')));
        } catch (RoleAlreadyExistsException $e) {
            return (new JsonResponse('already_exists'))
                ->setHttpStatus(Response::HTTP_CONFLICT)
                ->addNotification(new Error(__('msg.admin.users.roles.already_exists_with_name', [
                    'name' => $e->getCause()
                ])));
        }
    }

    public function updatePermissions(Request $request, UpdatePermissionsHandler $updatePermissionsHandler): JsonResponse
    {
        try {
            $updatePermissionsHandler->handle((int)$request->route('role'), $request->get('permissions'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Success(__('msg.admin.users.roles.successfully_updated')));
        } catch (RoleNotFoundException $e) {
            return (new JsonResponse('role_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.users.roles.not_found')));
        } catch (PermissionNotFoundException $e) {
            return (new JsonResponse('permission_not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.users.roles.permission_not_found_with_name', ['name' => $e->getCause()])));
        }
    }

    public function delete(Request $request, DeleteHandler $handler): JsonResponse
    {
        try {
            $handler->handle((int)$request->route('role'));

            return (new JsonResponse(Status::SUCCESS))
                ->addNotification(new Info(__('msg.admin.users.roles.successfully_deleted')));
        } catch (RoleNotFoundException $e) {
            return (new JsonResponse('not_found'))
                ->setHttpStatus(Response::HTTP_NOT_FOUND)
                ->addNotification(new Error(__('msg.admin.users.roles.not_found')));
        }
    }
}
