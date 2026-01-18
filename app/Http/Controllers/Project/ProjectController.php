<?php

namespace App\Http\Controllers\Project;

use App\Domains\Project\Actions\StatusUpdateProjectAction;
use App\Domains\Project\Actions\StoreProjectAction;
use App\Domains\Project\Actions\UpdateProjectAction;
use App\Domains\Project\DataTransferObjects\StatusUpdateProjectData;
use App\Domains\Project\DataTransferObjects\StoreProjectData;
use App\Domains\Project\DataTransferObjects\UpdateProjectData;
use App\Domains\Project\Model\Project;
use App\Domains\Project\Resources\ProjectResource;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
        $this->middleware('can:updateStatus,project')->only(['updateStatus']);
    }

    public function index()
    {
        return response()->json(ProjectResource::collection(Project::all()));
    }
    public function show(Project $project)
    {
        return response()->json(new ProjectResource($project));
    }
    public function store(StoreProjectData $data)
    {
        return response()->json([
            'message' => 'Проект успешно создан!',
            'project' => new ProjectResource(StoreProjectAction::execute($data))
        ]);
    }
    public function update(UpdateProjectData $data, Project $project)
    {
        return response()->json([
            'message' => 'Проект успешно обновлен!',
            'project' => new ProjectResource(UpdateProjectAction::execute($data, $project))
        ]);
    }
    public function destroy(Project $project)
    {
        $project->delete();
        return response()->json([
            'message' => 'Проект успешно удален!'
        ]);
    }

    public function updateStatus(StatusUpdateProjectData $data, Project $project)
    {
        return response()->json([
            'message' => 'Статус проекта успешно обновлен!',
            'project' => new ProjectResource(StatusUpdateProjectAction::execute($data, $project))
        ]);
    }
}
