<?php

namespace App\Http\Controllers\User;

use App\Domains\Shared\Exceptions\ConflictHttpException;
use App\Domains\Shared\Exceptions\ValidationFailedException;
use App\Domains\Shared\Model\Role;
use App\Domains\TimeEntry\Resources\TimeEntryResource;
use App\Domains\User\Actions\Admin\BanUserAction;
use App\Domains\User\Actions\Admin\CreateRoleUserAction;
use App\Domains\User\Actions\Admin\ForceStopUserTimeEntryAction;
use App\Domains\User\Actions\Admin\GetSystemStatsAction;
use App\Domains\User\Actions\Admin\GetUserStatsAction;
use App\Domains\User\Actions\Admin\UnbanUserAction;
use App\Domains\User\Actions\Admin\UpdateRoleUserAction;
use App\Domains\User\DataTransferObjects\CreateRoleUserData;
use App\Domains\User\DataTransferObjects\FilterUserData;
use App\Domains\User\DataTransferObjects\UpdateRoleUserData;
use App\Domains\User\Models\User;
use App\Domains\User\Resources\ProfileResource;
use App\Http\Controllers\Controller;

class AdminController extends Controller
{
    public function __construct()
    {
        $this->middleware('can:admin-access');
    }

    public function updateRole(UpdateRoleUserData $data, User $user)
    {
        return response()->json([
            "message" => 'Роль успешно обновлена!',
            'user' => new ProfileResource(UpdateRoleUserAction::execute($data, $user)),
        ]);
    }

    public function getRoles()
    {
        return response()->json([
            'roles' => Role::all()
        ]);
    }

    public function addRole(CreateRoleUserData $data)
    {
        return response()->json([
            "message" => 'Роль успешно создана!',
            "role" => CreateRoleUserAction::execute($data),
        ]);
    }

    public function deleteRole(Role $role)
    {
        if(User::where('role_id', $role->id)->exists())throw new ConflictHttpException(409, 'Нельзя удалить роль, у которой есть пользователи!');
        $role->delete();
        return response()->json([], 204);
    }

    public function getUsers(FilterUserData $filter)
    {
        $users = User::query()
            ->filter($filter)
            ->paginate(15);

        return ProfileResource::collection($users);
    }

    public function banUser(User $user)
    {
        return response()->json([
            "message" => "Вы успешно забанили пользователя!",
            "user" => new ProfileResource(BanUserAction::execute($user)),
        ]);
    }

    public function unbanUser(User $user)
    {
        return response()->json([
            "message" => "Вы успешно разбанили пользователя!",
            "user" => new ProfileResource(UnbanUserAction::execute($user)),
        ]);
    }

    public function getSystemStat()
    {
        return response()->json([
            GetSystemStatsAction::execute()
        ]);
    }

    public function getUserStat(User $user)
    {
        return response()->json([
            GetUserStatsAction::execute($user)
        ]);
    }

    public function forceStopTimeEntry(User $user)
    {
        return response()->json([
            "message" => "Запесь времени принудительно остановлена!",
            "time_entry" => new TimeEntryResource(ForceStopUserTimeEntryAction::execute($user)),
        ]);
    }
}
