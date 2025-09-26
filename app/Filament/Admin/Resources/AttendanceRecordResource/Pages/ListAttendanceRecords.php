<?php

namespace App\Filament\Admin\Resources\AttendanceRecordResource\Pages;

use App\Filament\Admin\Resources\AttendanceRecordResource;
use App\Models\AttendanceRecord;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;
use Filament\Resources\Components\Tab;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;

use Carbon\Carbon;

class ListAttendanceRecords extends ListRecords
{
    protected static string $resource = AttendanceRecordResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
            Actions\Action::make('quick_clock_in')
                ->label('Quick Clock In')
                ->icon('heroicon-o-play')
                ->color('success')
                ->form([
                    \Filament\Forms\Components\Select::make('user_id')
                        ->label('Employee')
                        ->options(\App\Models\User::where('is_active', true)->pluck('name', 'id'))
                        ->required()
                        ->searchable(),
                ])
                ->action(function (array $data) {
                    AttendanceRecord::updateOrCreate(
                        [
                            'user_id' => $data['user_id'],
                            'work_date' => today(),
                        ],
                        [
                            'clock_in' => now()->format('H:i:s'),
                            'status' => 'present',
                            'recorded_by' => Auth::id(),
                        ]
                    );
                }),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'today' => Tab::make('Today')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('work_date', Carbon::today()))
                ->badge(AttendanceRecord::whereDate('work_date', today())->count()),
            'this_week' => Tab::make('This Week')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereBetween('work_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]))
                ->badge(AttendanceRecord::whereBetween('work_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ])->count()),
            'present' => Tab::make('Present Today')
                ->modifyQueryUsing(fn (Builder $query) => $query->whereDate('work_date', Carbon::today())->where('status', 'present'))
                ->badge(AttendanceRecord::whereDate('work_date', today())->where('status', 'present')->count())
                ->badgeColor('success'),
        ];
    }
}