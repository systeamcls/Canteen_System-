<x-filament-panels::page>
    <script>
        // Simple function to update date to today if it's not current
        function ensureTodayDate() {
            const today = new Date().toISOString().split('T')[0];
            const currentDate = '{{ $selectedDate }}';
            
            if (currentDate !== today) {
                // Update the URL to include today's date
                const url = new URL(window.location);
                url.searchParams.set('selectedDate', today);
                window.location.href = url.toString();
            }
        }

        // Run when page loads
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(ensureTodayDate, 500);
        });

        // Check every 60 seconds for new day
        setInterval(ensureTodayDate, 60000);
    </script>

    <div>

    <script>
        function markAttendance(employeeId, status) {
            // Make an AJAX call to update attendance
            fetch('{{ route("filament.admin.pages.daily-attendance") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                },
                body: JSON.stringify({
                    employee_id: employeeId,
                    status: status,
                    date: '{{ $selectedDate }}'
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to refresh attendance data
                    location.reload();
                } else {
                    alert('Error updating attendance');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error updating attendance');
            });
        }
    </script>

    <style>
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1rem;
            margin-bottom: 2rem;
        }
        
        .stats-card {
            background: white;
            border-radius: 0.5rem;
            padding: 1.5rem;
            display: flex;
            align-items: center;
            gap: 1rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }
        
        .stats-card.present {
            background-color: #f0fdf4;
        }
        
        .stats-card.late {
            background-color: #fefce8;
        }
        
        .stats-card.half-day {
            background-color: #eff6ff;
        }
        
        .stats-card.absent {
            background-color: #fef2f2;
        }
        
        .stats-icon {
            width: 2rem;
            height: 2rem;
            flex-shrink: 0;
        }
        
        .stats-icon.present {
            color: #16a34a;
        }
        
        .stats-icon.late {
            color: #ea580c;
        }
        
        .stats-icon.half-day {
            color: #2563eb;
        }
        
        .stats-icon.absent {
            color: #dc2626;
        }
        
        .stats-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
            color: #111827;
        }
        
        .stats-label {
            font-size: 0.875rem;
            color: #6b7280;
            margin-top: 0.25rem;
        }

        /* Employee Section */
        .employee-container {
            background: white;
            border-radius: 0.5rem;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);
        }

        .employee-header {
            padding: 1.5rem;
            border-bottom: 1px solid #e5e7eb;
        }

        .employee-title {
            font-size: 1.125rem;
            font-weight: 500;
            color: #111827;
            margin: 0;
        }

        .employee-subtitle {
            margin-top: 0.25rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .employee-content {
            padding: 1.5rem;
        }

        .employee-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 0;
            border-bottom: 1px solid #f3f4f6;
        }

        .employee-row:last-child {
            border-bottom: none;
        }

        .employee-info {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .employee-avatar {
            width: 2.5rem;
            height: 2.5rem;
            background-color: #dbeafe;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .employee-initials {
            font-size: 0.875rem;
            font-weight: 500;
            color: #1d4ed8;
        }

        .employee-name {
            font-size: 1rem;
            font-weight: 500;
            color: #111827;
            margin: 0;
        }

        .employee-rate {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }

        .employee-actions {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .employee-status {
            font-size: 0.875rem;
            color: #6b7280;
            min-width: 100px;
            text-align: center;
        }

        .employee-status.active {
            font-weight: 500;
        }

        .button-group {
            display: flex;
            gap: 0.5rem;
        }

        .attendance-btn {
            padding: 0.375rem 0.75rem;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 0.25rem;
            border: 1px solid #d1d5db;
            background: white;
            color: #374151;
            cursor: pointer;
            transition: all 0.2s;
        }

        .attendance-btn:hover {
            background-color: #f9fafb;
        }

        .attendance-btn:disabled {
            opacity: 0.5;
            cursor: not-allowed;
        }

        .attendance-btn.present {
            background-color: #16a34a;
            color: white;
            border-color: #16a34a;
        }

        .attendance-btn.late {
            background-color: #ea580c;
            color: white;
            border-color: #ea580c;
        }

        .attendance-btn.half-day {
            background-color: #2563eb;
            color: white;
            border-color: #2563eb;
        }

        .attendance-btn.absent {
            background-color: #dc2626;
            color: white;
            border-color: #dc2626;
        }

        .loading-text {
            display: none;
        }

        .attendance-btn[disabled] .loading-text {
            display: inline;
        }

        .attendance-btn[disabled] .button-text {
            display: none;
        }
    </style>

    <div class="space-y-6">
        <!-- Date Selector -->
        <div class="bg-white rounded-lg shadow p-6">
            {{ $this->form }}
        </div>

        <!-- Stats Cards -->
        <div class="stats-grid">
            <div class="stats-card present">
                <svg class="stats-icon present" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                </svg>
                <div>
                    <div class="stats-number">{{ collect($attendanceData)->where('status', 'present')->count() }}</div>
                    <div class="stats-label">Present</div>
                </div>
            </div>

            <div class="stats-card late">
                <svg class="stats-icon late" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <div>
                    <div class="stats-number">{{ collect($attendanceData)->where('status', 'late')->count() }}</div>
                    <div class="stats-label">Late</div>
                </div>
            </div>

            <div class="stats-card half-day">
                <svg class="stats-icon half-day" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                </svg>
                <div>
                    <div class="stats-number">{{ collect($attendanceData)->where('status', 'half_day')->count() }}</div>
                    <div class="stats-label">Half Day</div>
                </div>
            </div>

            <div class="stats-card absent">
                <svg class="stats-icon absent" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
                <div>
                    <div class="stats-number">{{ collect($attendanceData)->where('status', 'absent')->count() }}</div>
                    <div class="stats-label">Absent</div>
                </div>
            </div>
        </div>

        <!-- Employee Attendance -->
        <div class="employee-container">
            <div class="employee-header">
                <h3 class="employee-title">Employee Attendance</h3>
                <p class="employee-subtitle">Click the buttons to mark attendance for each employee</p>
            </div>

            <div class="employee-content">
                @forelse($employees as $employee)
                    <div class="employee-row">
                        <!-- Employee Info -->
                        <div class="employee-info">
                            <div class="employee-avatar">
                                <span class="employee-initials">
                                    {{ strtoupper(substr($employee->name, 0, 2)) }}
                                </span>
                            </div>
                            <div>
                                <div class="employee-name">{{ $employee->name }}</div>
                                <div class="employee-rate">Daily Rate: ₱{{ number_format($employee->daily_rate ?? 500, 0) }}</div>
                            </div>
                        </div>

                        <!-- Status and Buttons -->
                        <div class="employee-actions">
                            @php
                                $currentStatus = $attendanceData[$employee->id]['status'] ?? null;
                                $employeeStatus = $attendanceData[$employee->id]['status'] ?? '';
                            @endphp
                            
                            <!-- Current Status -->
                            <div class="employee-status {{ $currentStatus ? 'active' : '' }}">
                                @if($currentStatus)
                                    {{ ucfirst(str_replace('_', ' ', $currentStatus)) }}
                                @else
                                    Not marked
                                @endif
                            </div>

                            <!-- Action Buttons -->
                            <div class="button-group">
                                <button 
                                    wire:click="markAttendance({{ $employee->id }}, 'present')"
                                    wire:loading.attr="disabled"
                                    wire:target="markAttendance"
                                    class="attendance-btn {{ $employeeStatus === 'present' ? 'present' : '' }}"
                                >
                                    <span class="button-text">Present</span>
                                    <span class="loading-text">...</span>
                                </button>

                                <button 
                                    wire:click="markAttendance({{ $employee->id }}, 'late')"
                                    wire:loading.attr="disabled"
                                    wire:target="markAttendance"
                                    class="attendance-btn {{ $employeeStatus === 'late' ? 'late' : '' }}"
                                >
                                    <span class="button-text">Late</span>
                                    <span class="loading-text">...</span>
                                </button>

                                <button 
                                    wire:click="markAttendance({{ $employee->id }}, 'half_day')"
                                    wire:loading.attr="disabled"
                                    wire:target="markAttendance"
                                    class="attendance-btn {{ $employeeStatus === 'half_day' ? 'half-day' : '' }}"
                                >
                                    <span class="button-text">Half Day</span>
                                    <span class="loading-text">...</span>
                                </button>

                                <button 
                                    wire:click="markAttendance({{ $employee->id }}, 'absent')"
                                    wire:loading.attr="disabled"
                                    wire:target="markAttendance"
                                    class="attendance-btn {{ $employeeStatus === 'absent' ? 'absent' : '' }}"
                                >
                                    <span class="button-text">Absent</span>
                                    <span class="loading-text">...</span>
                                </button>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-8">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">No staff members found</h3>
                        <p class="mt-1 text-sm text-gray-500">Add staff members to start tracking attendance.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Summary -->
        @if(count($employees) > 0)
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-lg font-medium text-gray-900 mb-4">Daily Summary</h3>
                <div class="grid grid-cols-3 gap-4 text-center">
                    <div>
                        <div class="text-2xl font-bold text-gray-900">{{ count($employees) }}</div>
                        <div class="text-sm text-gray-500">Total Employees</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-blue-600">
                            {{ count($employees) > 0 ? round((collect($attendanceData)->whereNotNull('status')->count() / count($employees)) * 100, 1) : 0 }}%
                        </div>
                        <div class="text-sm text-gray-500">Attendance Rate</div>
                    </div>
                    <div>
                        <div class="text-2xl font-bold text-green-600">
                            ₱{{ number_format(collect($employees)->sum(function ($employee) use ($attendanceData) {
                                $status = $attendanceData[$employee->id]['status'] ?? null;
                                $rate = $employee->daily_rate ?? 500;
                                return match($status) {
                                    'present', 'late' => $rate,
                                    'half_day' => $rate / 2,
                                    default => 0
                                };
                            }), 0) }}
                        </div>
                        <div class="text-sm text-gray-500">Daily Payroll</div>
                    </div>
                </div>
            </div>
        @endif
    </div>
    </div>
</x-filament-panels::page>