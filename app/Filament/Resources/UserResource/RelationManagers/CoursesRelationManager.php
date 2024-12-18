<?php

namespace App\Filament\Resources\UserResource\RelationManagers;

use App\Models\Course;
use App\Models\User;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Shield\Contracts\HasRoles;
use Illuminate\Database\Eloquent\Model;

class CoursesRelationManager extends RelationManager
{
    protected static string $relationship = 'courses';

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),

            ]);
    }

    public function table(Table $table): Table
    {
        return $table
            //->recordTitleAttribute('name')
            //->columns([
              //  Tables\Columns\TextColumn::make('name'),
            //])
            ->columns([
                Tables\Columns\TextColumn::make('name')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('letter')
                ->sortable()
                ->searchable()
                ->toggleable(isToggledHiddenByDefault: false),
                    Tables\Columns\TextColumn::make('season.name')//nombre de la relación del modelo + . + campo a visualizar
                    ->sortable()
                    ->searchable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                //Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    //Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
public static function canViewForRecord(Model $ownerRecord, string $pageClass): bool
    {
        return $ownerRecord->hasRole('athlete');
    }
}