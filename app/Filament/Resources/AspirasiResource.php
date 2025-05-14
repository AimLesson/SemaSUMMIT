<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Aspirasi;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Model;
use Filament\GlobalSearch\Actions\Action;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Contracts\Support\Htmlable;
use App\Filament\Resources\AspirasiResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\AspirasiResource\RelationManagers;

class AspirasiResource extends Resource
{
    protected static ?string $model = Aspirasi::class;


    public static function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Hidden::make('id_user')
                ->default(auth()->id())
                ->dehydrated()
                ->required(),

            Forms\Components\Placeholder::make('user_display')
                ->label('Author')
                ->content(fn() => auth()->user()?->name),

            Forms\Components\Select::make('category')
                ->label('Kategori')
                ->required()
                ->options([
                    'Saran' => 'Saran',
                    'Kritik' => 'Kritik',
                    'Aspirasi' => 'Aspirasi',
                ]),

            Forms\Components\FileUpload::make('image')
                ->image()->columnSpan('full')
                ->label('Gambar'),

            Forms\Components\RichEditor::make('content')
                ->label('Isi Aspirasi')->columnSpan('full')
                ->required(),

            Forms\Components\Toggle::make('is_anonymous')
                ->label('Anonim?')
                ->inline(),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\TextColumn::make('category')
                    ->label('Kategori')
                    ->sortable()
                    ->searchable(),

                Tables\Columns\IconColumn::make('is_anonymous')
                    ->label('Anonim')
                    ->boolean(),

                Tables\Columns\ToggleColumn::make('is_approved')
                    ->label('Disetujui')
                    ->visible(fn () => auth()->user()->hasRole(['admin', 'super_admin']))
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                // Tables\Actions\BulkActionGroup::make([
                //     Tables\Actions\DeleteBulkAction::make(),
                // ]),
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
            'index' => Pages\ListAspirasis::route('/'),
            'create' => Pages\CreateAspirasi::route('/create'),
            // 'view' => Pages\ViewAspirasi::route('/{record}'),
            // 'edit' => Pages\EditAspirasi::route('/{record}/edit'),
        ];
    }

    public static function getGlobalSearchResultTitle(Model $record): string|Htmlable
    {
        return $record->content;
    }

    public static function getGloballySearchableAttributes(): array
    {
        return ['content', 'user.name', 'category'];
    }

    public static function getGlobalSearchResultDetails(Model $record): array
    {
        return [
            'Author' => $record->user->name,
            'Category' => $record->category,
            'Content' => $record->content,
        ];
    }

    public static function getGlobalSearchResultActions(Model $record): array
{
    return [
        Action::make('view')
            ->url(static::getUrl('view', ['record' => $record])),
    ];
}
}
