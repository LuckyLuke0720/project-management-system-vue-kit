<?php

namespace App\Filament\Resources;

use App\Filament\Resources\CommentsResource\Pages;
use App\Filament\Resources\CommentsResource\RelationManagers;
use App\Models\Comments;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\Select;
use App\Models\Project;
use App\Models\Task;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class CommentsResource extends Resource
{
    protected static ?string $model = Comments::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Textarea::make('content')
                    ->required()
                    ->columnSpanFull(),
                
                Select::make('task_id')
                    ->label('Task (title)')
                    ->relationship('task', 'title')
                    ->searchable()
                    ->preload()
                    ->required()
                    ->afterStateUpdated(function ($state, callable $set) {
                        if ($state) {
                            $task = Task::find($state);
                            if ($task) {
                                $set('project_id', $task->project_id);
                            }
                        }
                    }),
                
                Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->required(),

                // Hidden field to store the project_id for filtering
                Select::make('project_id')
                    ->label('Project')
                    ->options(function () {
                        return Project::pluck('title', 'id');
                    })
                    ->searchable()
                    ->preload()
                    ->required()
                    ->reactive()
                    ->afterStateUpdated(function ($state, callable $set) {
                        $set('task_id', null);
                    })
                    ->hidden(fn ($record) => $record !== null),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('content')
                    ->limit(50)
                    ->wrap()
                    ->searchable()
                    ->tooltip(fn (string $state): string => $state),
                
                TextColumn::make('task.title')
                    ->label('Task')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('task.project.title')
                    ->label('Project')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),
                
                TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable(),
            ])->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('project')
                    ->relationship('task.project', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Project'),
                
                SelectFilter::make('task')
                    ->relationship('task', 'title')
                    ->searchable()
                    ->preload()
                    ->label('Filter by Task'),
                
                SelectFilter::make('user')
                    ->relationship('user', 'name')
                    ->searchable()
                    ->preload()
                    ->label('Filter by User'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                \Filament\Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
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
            'index' => Pages\ListComments::route('/'),
            'create' => Pages\CreateComments::route('/create'),
            'edit' => Pages\EditComments::route('/{record}/edit'),
        ];
    }
}
