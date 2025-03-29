<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Forms\Components\Select;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class ProjectResource extends Resource
{
    protected static ?string $model = Project::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('title')->required(),
                Textarea::make('description')->required()->columnSpanFull(),
                
                Repeater::make('projectUsers')
                    ->relationship()
                    ->schema([
                        Select::make('user_id')
                            ->label('User')
                            ->relationship('user', 'name')
                            ->options(fn () => User::pluck('name', 'id')) 
                            ->searchable()
                            ->preload()
                            ->required(),
                        Select::make('role')
                            ->options([
                                'Owner' => 'Owner',
                                'Member' => 'Member',
                            ])
                            ->default('Member')
                            ->required(),
                    ])
                    ->addActionLabel('Add user to project')
                    ->columns(2),

                #TODO: finish implementing and bugfixing adding modifying a task
                // Repeater::make('tasks')
                //     ->relationship()
                //     ->schema([
                //         TextInput::make('title')
                //             ->required(),
                        
                //         Textarea::make('description')
                //             ->required(),
                        
                //         DatePicker::make('due_date')
                //             ->required(),

                //         Select::make('status')
                //             ->options([
                //                 'To Do'=> 'To Do',
                //                 'In Progress'=> 'In Progress',
                //                 'Under Review'=>'Under Review',
                //                 'Completed'=>'Completed',
                //             ])
                //             ->default('To Do')
                //             ->required(),
                        
                //         TextInput::make('order')
                //             ->default(fn ($livewire) => $livewire->ownerRecord->tasks()->count() + 1)
                //             ->hidden(),
                        
                //         Select::make('assignee_user_id')
                //             ->label('Assign To')
                //             ->options(function ($livewire) {
                //                 // Only show users that are members of this project
                //                 $projectId = $livewire->ownerRecord->name ?? null;
                                
                //                 if (!$projectId) {
                //                     return [];
                //                 }
                                
                //                 return Project::find($projectId)
                //                     ->projectUsers()
                //                     ->with('user')
                //                     ->get()
                //                     ->pluck('user.name', 'user.id');
                //             })
                //             ->searchable()
                //             ->preload()
                //             ->required(),
                // ])
                // ->addActionLabel('Add task to project')
                // ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
{
    return $table
        ->columns([
            TextColumn::make('title')
                ->sortable()
                ->searchable()
                ->wrap(),

            TextColumn::make('description')
                ->limit(50)
                ->wrap()
                ->searchable()
                ->tooltip(fn (string $state): string => $state), //description on hover

            TextColumn::make('tasks_count')
                ->label('Tasks')
                ->getStateUsing(fn (Project $record): int => $record->tasks()->count())//no of attached tasks
                ->sortable(),

            TextColumn::make('members_count')
                ->label('Members')
                ->getStateUsing(fn (Project $record): int => $record->members()->count())//no of attached users
                ->sortable(),
        ])
        ->defaultSort('id', 'asc')
        ->striped() 
        ->paginated(10); 
}

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListProjects::route('/'),
            'create' => Pages\CreateProject::route('/create'),
            'edit' => Pages\EditProject::route('/{record}/edit'),
        ];
    }
}
