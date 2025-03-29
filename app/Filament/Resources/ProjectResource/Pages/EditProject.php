<?php

namespace App\Filament\Resources\ProjectResource\Pages;

use App\Filament\Resources\ProjectResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Validation\ValidationException;

class EditProject extends EditRecord
{
    protected static string $resource = ProjectResource::class;

    protected function mutateFormDataBeforeSave(array $data): array
{
    $project = $this->getRecord();

    if (isset($data['tasks']) && !empty($data['tasks'])) {
        if ($project->projectUsers()->count() === 0) {
            throw new ValidationException(
                validator: validator([], []),
                response: redirect()->back()->withErrors(['tasks' => 'Cannot add tasks to a project without users.'])
            );
        }
    }

    return $data;
}


    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}