<?php

namespace App\Filament\Resources;

use App\Filament\Resources\ProjectResource\Pages;
use App\Models\Project;
use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Resources\Resource;
use Filament\Support\Enums\Alignment;
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
                    ->live()
                    ->addActionLabel('Add user to project')
                    ->addActionAlignment(Alignment::Start)
                    ->columns(2)
                    ->afterStateUpdated(fn (Set $set) => $set('assignee_user_id', null)), // reset assign field on change to not enable AssignTo early (does not seem to work)

                    Repeater::make('tasks')
                    ->relationship()
                    ->schema([
                        TextInput::make('title')
                            ->required(),
                        
                        Textarea::make('description')
                            ->required(),
                        
                        DatePicker::make('due_date')
                            ->required(),

                        Select::make('status')
                            ->options([
                                'To Do'=> 'To Do',
                                'In Progress'=> 'In Progress',
                                'Under Review'=>'Under Review',
                                'Completed'=>'Completed',
                            ])
                            ->default('To Do')
                            ->required(),
                        
                        TextInput::make('order')
                        ->default(function (Get $get, $state, $record = null) {
                            //projectId from the current context
                            $projectId = null;
                                
                            // editing an existing task
                            if ($record instanceof \App\Models\Task) {
                                $projectId = $record->project_id;
                            } 
                            // in a project context
                            else if ($get('../../id')) {
                                $projectId = $get('../../id');
                            }
                                
                            if ($projectId) {
                                return \App\Models\Task::where('project_id', $projectId)->count() + 1;
                            }
                            
                               return 1;
                        })
                        ->dehydrated(true) // ensure the value is saved to the database
                        ->hidden(),
                        
                        Select::make('assignee_user_id')
                            ->label('Assign To')
                            ->relationship('assignee', 'name')
                            ->options(function (Get $get, $record = null) {
                                
                                $project = null;
        
                                
                                if ($record instanceof \App\Models\Task) {
                                
                                    $project = $record->project;
                                } else {
                                
                                    $project = $get('../../id') ? Project::find($get('../../id')) : null;
                                }
        
                                if (!$project) {
                                    return []; // project context not found
                                }
        
                                // get users associated with this project
                                $projectUsers = $project->projectUsers()
                                    ->with('user')
                                    ->get();
        
                                if ($projectUsers->isEmpty()) {
                                    return []; 
                                }
        
                                return $projectUsers->pluck('user.name', 'user.id');
                            })
                            ->searchable()
                            ->preload()
                            ->required()
                            ->live() 
                            ->afterStateHydrated(function (Set $set, Get $get) {
                                
                                $users = $get('../../projectUsers');
                                $hasUsers = !empty($users);
                        
                                if (!$hasUsers) {
                                    $set('assignee_user_id', null); // reset selection if no users
                                }
                            })
                            ->disabled(fn (Get $get) => empty($get('../../projectUsers')))
                            ->hint(fn (Get $get) => empty($get('../../projectUsers')) ? '⚠ No users assigned to this project. Please add users first.' : null)
                    ])
                    ->addActionLabel('Add task to project')
                    ->addActionAlignment(Alignment::Start)
                    ->columnSpanFull(),
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
        // ->defaultSort('tasks_count', 'asc')
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
