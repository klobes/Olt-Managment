<?php

namespace Botble\FiberhomeOltManager\Services;

use Botble\FiberhomeOltManager\Models\FiberCable;
use Botble\FiberhomeOltManager\Models\JunctionBox;
use Botble\FiberhomeOltManager\Models\Splitter;
use Botble\FiberhomeOltManager\Models\SpliceCassette;
use Botble\FiberhomeOltManager\Models\CableSegment;
use Botble\FiberhomeOltManager\Models\FiberSplice;
use Botble\FiberhomeOltManager\Models\SplitterConnection;
use Botble\FiberhomeOltManager\Models\OltDevice;
use Botble\FiberhomeOltManager\Models\Onu;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Log;

class TopologyService
{
    /**
     * Create a new fiber cable
     */
    public function createFiberCable(array $data): FiberCable
    {
        return FiberCable::create($data);
    }

    /**
     * Create a new junction box
     */
    public function createJunctionBox(array $data): JunctionBox
    {
        return JunctionBox::create($data);
    }

    /**
     * Create a new splitter
     */
    public function createSplitter(array $data): Splitter
    {
        return Splitter::create($data);
    }

    /**
     * Create a new splice cassette
     */
    public function createSpliceCassette(array $data): SpliceCassette
    {
        return SpliceCassette::create($data);
    }

    /**
     * Create a new cable segment
     */
    public function createCableSegment(array $data): CableSegment
    {
        return CableSegment::create($data);
    }

    /**
     * Create a new fiber splice
     */
    public function createFiberSplice(array $data): FiberSplice
    {
        return FiberSplice::create($data);
    }

    /**
     * Create a new splitter connection
     */
    public function createSplitterConnection(array $data): SplitterConnection
    {
        return SplitterConnection::create($data);
    }

    /**
     * Get topology overview
     */
    public function getTopologyOverview(): array
    {
        return Cache::remember('topology_overview', 3600, function () {
            return [
                'olt_devices' => OltDevice::where('is_active', true)->count(),
                'fiber_cables' => FiberCable::where('status', 'active')->count(),
                'junction_boxes' => JunctionBox::where('status', 'active')->count(),
                'splitters' => Splitter::where('status', 'active')->count(),
                'splice_cassettes' => SpliceCassette::where('status', 'active')->count(),
                'cable_segments' => CableSegment::where('status', 'active')->count(),
                'total_fiber_length' => $this->getTotalFiberLength(),
                'total_splitter_ports' => $this->getTotalSplitterPorts(),
                'utilization_stats' => $this->getUtilizationStats(),
            ];
        });
    }

    /**
     * Get equipment utilization statistics
     */
    private function getUtilizationStats(): array
    {
        return [
            'junction_boxes' => [
                'total' => JunctionBox::where('status', 'active')->count(),
                'full' => JunctionBox::where('status', 'active')
                    ->whereRaw('used_capacity >= capacity')
                    ->count(),
                'average_utilization' => JunctionBox::where('status', 'active')
                    ->avg('used_capacity / capacity * 100'),
            ],
            'splitters' => [
                'total' => Splitter::where('status', 'active')->count(),
                'full' => Splitter::where('status', 'active')
                    ->whereRaw('used_output_ports >= output_ports')
                    ->count(),
                'average_utilization' => Splitter::where('status', 'active')
                    ->avg('used_output_ports / output_ports * 100'),
            ],
            'splice_cassettes' => [
                'total' => SpliceCassette::where('status', 'active')->count(),
                'full' => SpliceCassette::where('status', 'active')
                    ->whereRaw('used_capacity >= capacity')
                    ->count(),
                'average_utilization' => SpliceCassette::where('status', 'active')
                    ->avg('used_capacity / capacity * 100'),
            ],
        ];
    }

    /**
     * Get total fiber length
     */
    private function getTotalFiberLength(): float
    {
        return FiberCable::where('status', 'active')->sum('length');
    }

    /**
     * Get total splitter ports
     */
    private function getTotalSplitterPorts(): int
    {
        return Splitter::where('status', 'active')->sum('output_ports');
    }

    /**
     * Find available equipment near location
     */
    public function findAvailableEquipment(float $latitude, float $longitude, float $radius = 1.0): array
    {
        // Convert radius from km to degrees (approximately)
        $latRadius = $radius / 111.0; // 1 degree latitude ≈ 111 km
        $lonRadius = $radius / (111.0 * cos(deg2rad($latitude)));

        return [
            'junction_boxes' => JunctionBox::where('status', 'active')
                ->whereBetween('latitude', [$latitude - $latRadius, $latitude + $latRadius])
                ->whereBetween('longitude', [$longitude - $lonRadius, $longitude + $lonRadius])
                ->whereRaw('used_capacity < capacity')
                ->get(),

            'splitters' => Splitter::where('status', 'active')
                ->whereBetween('latitude', [$latitude - $latRadius, $latitude + $latRadius])
                ->whereBetween('longitude', [$longitude - $lonRadius, $longitude + $lonRadius])
                ->whereRaw('used_output_ports < output_ports')
                ->get(),
        ];
    }

    /**
     * Get equipment recommendations for new connection
     */
    public function getEquipmentRecommendations(float $latitude, float $longitude): array
    {
        $availableEquipment = $this->findAvailableEquipment($latitude, $longitude);

        $recommendations = [];

        if ($availableEquipment['junction_boxes']->isEmpty()) {
            $recommendations[] = [
                'type' => 'junction_box',
                'priority' => 'high',
                'description' => 'No available junction boxes nearby. Consider installing new junction box.',
                'suggested_location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
            ];
        }

        if ($availableEquipment['splitters']->isEmpty()) {
            $recommendations[] = [
                'type' => 'splitter',
                'priority' => 'medium',
                'description' => 'No available splitters nearby. Consider installing new splitter.',
                'suggested_location' => [
                    'latitude' => $latitude,
                    'longitude' => $longitude,
                ],
            ];
        }

        // Recommend optimal equipment from available ones
        foreach ($availableEquipment['junction_boxes'] as $jb) {
            $utilization = $jb->used_capacity / $jb->capacity * 100;
            if ($utilization < 50) {
                $recommendations[] = [
                    'type' => 'junction_box',
                    'priority' => 'low',
                    'description' => "Junction box {$jb->box_code} has good capacity ({$utilization}% used)",
                    'equipment' => $jb,
                ];
            }
        }

        foreach ($availableEquipment['splitters'] as $splitter) {
            $utilization = $splitter->used_output_ports / $splitter->output_ports * 100;
            if ($utilization < 50) {
                $recommendations[] = [
                    'type' => 'splitter',
                    'priority' => 'low',
                    'description' => "Splitter {$splitter->splitter_code} has good capacity ({$utilization}% used)",
                    'equipment' => $splitter,
                ];
            }
        }

        return $recommendations;
    }

    /**
     * Validate topology consistency
     */
    public function validateTopology(): array
    {
        $errors = [];

        // Check for orphaned cable segments
        $orphanedSegments = CableSegment::where(function ($query) {
            $query->whereNull('source_cable_segment_id')
                ->where('source_type', '!=', 'OltDevice');
        })->orWhere(function ($query) {
            $query->whereNull('destination_cable_segment_id')
                ->where('destination_type', '!=', 'Onu');
        })->get();

        if ($orphanedSegments->isNotEmpty()) {
            $errors[] = [
                'type' => 'orphaned_segments',
                'description' => 'Found ' . $orphanedSegments->count() . ' orphaned cable segments',
                'severity' => 'warning',
            ];
        }

        // Check for capacity violations
        $overCapacityJunctionBoxes = JunctionBox::where('status', 'active')
            ->whereRaw('used_capacity >= capacity')
            ->get();

        if ($overCapacityJunctionBoxes->isNotEmpty()) {
            $errors[] = [
                'type' => 'capacity_violation',
                'description' => 'Found ' . $overCapacityJunctionBoxes->count() . ' junction boxes over capacity',
                'severity' => 'error',
            ];
        }

        // Check for splitter capacity violations
        $overCapacitySplitters = Splitter::where('status', 'active')
            ->whereRaw('used_output_ports >= output_ports')
            ->get();

        if ($overCapacitySplitters->isNotEmpty()) {
            $errors[] = [
                'type' => 'capacity_violation',
                'description' => 'Found ' . $overCapacitySplitters->count() . ' splitters over capacity',
                'severity' => 'error',
            ];
        }

        return $errors;
    }

    /**
     * Generate topology report
     */
    public function generateTopologyReport(array $filters = []): array
    {
        $report = [];

        $report['overview'] = $this->getTopologyOverview();
        $report['equipment_breakdown'] = $this->getEquipmentBreakdown();
        $report['utilization_analysis'] = $this->getUtilizationAnalysis();
        $report['geographic_distribution'] = $this->getGeographicDistribution();
        $report['validation_errors'] = $this->validateTopology();
        $report['recommendations'] = $this->generateRecommendations();

        return $report;
    }

    /**
     * Get equipment breakdown by type
     */
    private function getEquipmentBreakdown(): array
    {
        return [
            'fiber_cables' => [
                'by_type' => FiberCable::where('status', 'active')
                    ->selectRaw('cable_type, COUNT(*) as count')
                    ->groupBy('cable_type')
                    ->pluck('count', 'cable_type')
                    ->toArray(),
                'by_fiber_count' => FiberCable::where('status', 'active')
                    ->selectRaw('fiber_count, COUNT(*) as count')
                    ->groupBy('fiber_count')
                    ->pluck('count', 'fiber_count')
                    ->toArray(),
            ],
            'junction_boxes' => [
                'by_type' => JunctionBox::where('status', 'active')
                    ->selectRaw('box_type, COUNT(*) as count')
                    ->groupBy('box_type')
                    ->pluck('count', 'box_type')
                    ->toArray(),
            ],
            'splitters' => [
                'by_type' => Splitter::where('status', 'active')
                    ->selectRaw('splitter_type, COUNT(*) as count')
                    ->groupBy('splitter_type')
                    ->pluck('count', 'splitter_type')
                    ->toArray(),
            ],
        ];
    }

    /**
     * Get utilization analysis
     */
    private function getUtilizationAnalysis(): array
    {
        return [
            'junction_boxes' => $this->analyzeJunctionBoxUtilization(),
            'splitters' => $this->analyzeSplitterUtilization(),
            'splice_cassettes' => $this->analyzeSpliceCassetteUtilization(),
        ];
    }

    /**
     * Analyze junction box utilization
     */
    private function analyzeJunctionBoxUtilization(): array
    {
        $junctionBoxes = JunctionBox::where('status', 'active')->get();
        
        $utilization = $junctionBoxes->map(function ($jb) {
            return $jb->used_capacity / $jb->capacity * 100;
        });
        
        return [
            'average_utilization' => round($utilization->avg(), 2),
            'min_utilization' => round($utilization->min(), 2),
            'max_utilization' => round($utilization->max(), 2),
            'over_capacity' => $junctionBoxes->where('used_capacity', '>=', 'capacity')->count(),
            'under_utilized' => $junctionBoxes->where('used_capacity', '<', $jb->capacity * 0.5)->count(),
        ];
    }

    /**
     * Analyze splitter utilization
     */
    private function analyzeSplitterUtilization(): array
    {
        $splitters = Splitter::where('status', 'active')->get();
        
        $utilization = $splitters->map(function ($splitter) {
            return $splitter->used_output_ports / $splitter->output_ports * 100;
        });
        
        return [
            'average_utilization' => round($utilization->avg(), 2),
            'min_utilization' => round($utilization->min(), 2),
            'max_utilization' => round($utilization->max(), 2),
            'over_capacity' => $splitters->where('used_output_ports', '>=', 'output_ports')->count(),
            'under_utilized' => $splitters->where('used_output_ports', '<', $splitter->output_ports * 0.5)->count(),
        ];
    }

    /**
     * Analyze splice cassette utilization
     */
    private function analyzeSpliceCassetteUtilization(): array
    {
        $cassettes = SpliceCassette::where('status', 'active')->get();
        
        $utilization = $cassettes->map(function ($cassette) {
            return $cassette->used_capacity / $cassette->capacity * 100;
        });
        
        return [
            'average_utilization' => round($utilization->avg(), 2),
            'min_utilization' => round($utilization->min(), 2),
            'max_utilization' => round($utilization->max(), 2),
            'over_capacity' => $cassettes->where('used_capacity', '>=', 'capacity')->count(),
            'under_utilized' => $cassettes->where('used_capacity', '<', $cassette->capacity * 0.5)->count(),
        ];
    }

    /**
     * Get geographic distribution
     */
    private function getGeographicDistribution(): array
    {
        $junctionBoxes = JunctionBox::where('status', 'active')
            ->selectRaw('COUNT(*) as count, latitude, longitude')
            ->groupBy('latitude', 'longitude')
            ->get();
        
        $splitters = Splitter::where('status', 'active')
            ->selectRaw('COUNT(*) as count, latitude, longitude')
            ->groupBy('latitude', 'longitude')
            ->get();
        
        return [
            'junction_boxes' => $junctionBoxes->toArray(),
            'splitters' => $splitters->toArray(),
            'coverage_area' => $this->calculateCoverageArea($junctionBoxes),
        ];
    }

    /**
     * Calculate coverage area
     */
    private function calculateCoverageArea($locations): array
    {
        if ($locations->isEmpty()) {
            return ['min_lat' => null, 'max_lat' => null, 'min_lon' => null, 'max_lon' => null];
        }
        
        return [
            'min_lat' => $locations->min('latitude'),
            'max_lat' => $locations->max('latitude'),
            'min_lon' => $locations->min('longitude'),
            'max_lon' => $locations->max('longitude'),
        ];
    }

    /**
     * Generate recommendations
     */
    private function generateRecommendations(): array
    {
        $recommendations = [];
        
        $stats = $this->getTopologyOverview();
        
        // Check for capacity issues
        if ($stats['junction_boxes']['full'] > 0) {
            $recommendations[] = [
                'type' => 'capacity',
                'priority' => 'high',
                'description' => 'Found ' . $stats['junction_boxes']['full'] . ' junction boxes at full capacity. Consider adding new junction boxes.',
            ];
        }
        
        if ($stats['splitters']['full'] > 0) {
            $recommendations[] = [
                'type' => 'capacity',
                'priority' => 'high',
                'description' => 'Found ' . $stats['splitters']['full'] . ' splitters at full capacity. Consider adding new splitters.',
            ];
        }
        
        // Check for under-utilized equipment
        if ($stats['junction_boxes']['average_utilization'] < 30) {
            $recommendations[] = [
                'type' => 'optimization',
                'priority' => 'low',
                'description' => 'Junction boxes are under-utilized on average. Consider optimizing placement.',
            ];
        }
        
        // Check for maintenance needs
        $upcomingMaintenance = $this->getUpcomingMaintenance(90);
        if (count($upcomingMaintenance) > 5) {
            $recommendations[] = [
                'type' => 'maintenance',
                'priority' => 'medium',
                'description' => 'Found ' . count($upcomingMaintenance) . ' upcoming maintenance tasks. Consider scheduling.',
            ];
        }
        
        return $recommendations;
    }

    /**
     * Get upcoming maintenance
     */
    private function getUpcomingMaintenance(int $days): array
    {
        return app(MaintenanceService::class)->getUpcomingMaintenance($days);
    }
}