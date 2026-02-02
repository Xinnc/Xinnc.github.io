<?php

namespace App\Domains\Project\Actions;


use App\Domains\Project\DataTransferObjects\UpdateProjectData;
use App\Domains\Project\Model\Project;

class UpdateProjectAction
{
    public static function execute(UpdateProjectData $data, Project $project): Project
    {
        $project->ensureIsActive();
        $updates = $data->getFilledFields();

        if (!empty($updates)) {
            $project->update($updates);
        }

        return $project;
    }
}
