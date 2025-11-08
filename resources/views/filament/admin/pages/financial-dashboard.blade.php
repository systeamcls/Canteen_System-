<x-filament-panels::page>
    <style>
        /* Custom dashboard styling */
        .fi-section {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            border-radius: 12px;
            padding: 1.5rem;
            margin-bottom: 2rem;
            color: white;
        }

        .fi-section h2 {
            font-size: 1.875rem;
            font-weight: bold;
            margin-bottom: 0.5rem;
        }

        .fi-section p {
            opacity: 0.9;
            font-size: 0.875rem;
        }

        /* Widget card enhancements */
        .fi-wi-stats-overview-stat {
            transition: transform 0.2s, box-shadow 0.2s;
        }

        .fi-wi-stats-overview-stat:hover {
            transform: translateY(-4px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        /* Chart containers */
        .fi-wi-chart {
            background: white;
            border-radius: 12px;
            padding: 1.5rem;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }

        /* Table styling */
        .fi-ta-table {
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
        }
    </style>

    {{-- Dashboard Header --}}
    <div class="fi-section">
        <h2>📊 Financial Analytics Dashboard</h2>
        <p>
            Comprehensive financial overview • Real-time data tracking • Powered by your business insights
        </p>
    </div>

    {{-- All Widgets --}}
    <x-filament-widgets::widgets :columns="$this->getColumns()" :data="[...property_exists($this, 'filters') ? ['filters' => $this->filters] : [], ...$this->getWidgetData()]" :widgets="$this->getWidgets()" />
</x-filament-panels::page>
