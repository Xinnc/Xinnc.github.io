<?php

namespace App\Domains\Project\Actions;


use App\Domains\Project\DataTransferObjects\StatusUpdateProjectData;
use App\Domains\Project\Model\Project;

class StatusUpdateProjectAction
{
    public static function execute(StatusUpdateProjectData $data, Project $project): Project
    {
        $project->status = $data->status;
        $project->save();

        return $project;
    }
}
