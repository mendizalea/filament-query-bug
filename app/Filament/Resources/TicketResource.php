<?php

namespace App\Filament\Resources;

use App\Filament\Resources\TicketResource\Pages;
use App\Models\Ticket;
use Filament\Forms\Components\MarkdownEditor;
use Filament\Forms\Components\Placeholder;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Filament\Tables\Columns\TextColumn;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class TicketResource extends Resource
{
    protected static ?string $model = Ticket::class;

    protected static ?string $slug = 'tickets';

    protected static ?string $recordTitleAttribute = 'id';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('subject')
                    ->required(),

                Select::make('status')
                    ->options([
                        'pending' => 'pending',
                        'wait' => 'wait',
                        'active' => 'active',
                    ]),

                MarkdownEditor::make('description')
                    ->columnSpanFull()
                    ->required(),

                Select::make('requested_by')
                    ->relationship('requestedBy', 'name')
                    ->required(),

                Select::make('owned_by')
                    ->relationship('ownedBy', 'name')
                    ->required(),

                Placeholder::make('created_at')
                    ->label('Created Date')
                    ->content(fn(?Ticket $record
                    ): string => $record?->created_at?->diffForHumans() ?? '-'),

                Placeholder::make('updated_at')
                    ->label('Last Modified Date')
                    ->content(fn(?Ticket $record
                    ): string => $record?->updated_at?->diffForHumans() ?? '-'),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id'),

                TextColumn::make('subject'),

                TextColumn::make('description')
                    ->wrap(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('requestedBy.name'),

                TextColumn::make('ownedBy.name'),
            ])
            ->filters([
                SelectFilter::make('owned_by')
                    ->relationship('ownedBy', 'name'),
                SelectFilter::make('requested_by')
                    ->relationship('requestedBy', 'name'),
                SelectFilter::make('status')
                    ->options([
                        'pending' => 'pending',
                        'wait' => 'wait',
                        'active' => 'active',
                    ])
                    ->default(['wait'])
                    ->multiple(),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index'  => Pages\ListTickets::route('/'),
            'create' => Pages\CreateTicket::route('/create'),
            'edit'   => Pages\EditTicket::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->withoutGlobalScopes([SoftDeletingScope::class])
            ->whereBelongsTo(auth()->user(), 'ownedBy')
            ->orWhereBelongsTo(auth()->user(), 'requestedBy');
    }
}
