<?php

namespace Botble\FiberHomeOLTManager\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\FiberHomeOLTManager\Models\OLT;
use Botble\FiberHomeOLTManager\Models\ONU;
use Botble\FiberHomeOLTManager\Models\BandwidthProfile;
use Botble\FiberHomeOLTManager\Services\PerformanceMetricsService;
use Illuminate\Http\Request;

class DashboardController extends BaseController
{
    protected $metricsService;

    public function __construct(PerformanceMetricsService $metricsService)
    {
        $this->metricsService = $metricsService;
    }

    public function index()
    {
        page_title()->setTitle(trans('plugins/fiberhome-olt-manager::dashboard.title'));

        // Get statistics
        $stats = [
            'total_olts' => OLT::count(),
            'online_olts' => OLT::where('status', 'online')->count(),
            'total_onus' => ONU::count(),
            'online_onus' => ONU::where('status', 'online')->count(),
            'total_profiles' => BandwidthProfile::count(),
            'active_profiles' => BandwidthProfile::where('status', 'active')->count(),
        ];

        // Get recent alerts
        $alerts = $this->getRecentAlerts();

        // Get performance summary
        $performance = $this->metricsService->getSystemPerformanceSummary();

        return view('plugins/fiberhome-olt-manager::dashboard', compact('stats', 'alerts', 'performance'));
    }

    public function topology()
    {
        page_title()->setTitle(trans('plugins/fiberhome-olt-manager::topology.title'));

        $olts = OLT::with(['onus' => function ($query) {
            $query->select(['id', 'olt_id', 'serial_number', 'status', 'slot', 'port']);
        }])->get();

        return view('plugins/fiberhome-olt-manager::topology.index', compact('olts'));
    }

    private function getRecentAlerts()
    {
        $alerts = [];

        // Check OLT status alerts
        $offlineOlts = OLT::where('status', 'offline')
            ->where('last_polled', '<', now()->subMinutes(15))
            ->get();

        foreach ($offlineOlts as $olt) {
            $alerts[] = [
                'type' => 'danger',
                'icon' => 'fa fa-exclamation-triangle',
                'title' => trans('plugins/fiberhome-olt-manager::alerts.olt_offline'),
                'message' => trans('plugins/fiberhome-olt-manager::alerts.olt_offline_message', ['name' => $olt->name]),
                'time' => $olt->last_polled,
            ];
        }

        // Check ONU status alerts
        $offlineOnus = ONU::where('status', 'offline')
            ->where('last_seen', '<', now()->subMinutes(30))
            ->take(5)
            ->get();

        foreach ($offlineOnus as $onu) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa fa-warning',
                'title' => trans('plugins/fiberhome-olt-manager::alerts.onu_offline'),
                'message' => trans('plugins/fiberhome-olt-manager::alerts.onu_offline_message', ['serial' => $onu->serial_number]),
                'time' => $onu->last_seen,
            ];
        }

        // Check performance alerts
        $highCpuOlts = OLT::where('cpu_usage', '>', setting('fiberhome_alert_threshold_cpu', 80))
            ->get();

        foreach ($highCpuOlts as $olt) {
            $alerts[] = [
                'type' => 'warning',
                'icon' => 'fa fa-tachometer',
                'title' => trans('plugins/fiberhome-olt-manager::alerts.high_cpu_usage'),
                'message' => trans('plugins/fiberhome-olt-manager::alerts.high_cpu_message', ['name' => $olt->name, 'usage' => $olt->cpu_usage]),
                'time' => now(),
            ];
        }

        // Sort alerts by time (newest first)
        usort($alerts, function ($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        return array_slice($alerts, 0, 10); // Return only 10 most recent alerts
    }
}