<?php

namespace Botble\FiberHomeOLTManager\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Illuminate\Http\Request;

class SettingsController extends BaseController
{
    public function index()
    {
        page_title()->setTitle(trans('plugins/fiberhome-olt-manager::settings.title'));
        
        return view('plugins.fiberhome-olt-manager::settings.index');
    }

    public function update(Request $request, BaseHttpResponse $response)
    {
        try {
            $settings = $request->validate([
                'snmp_timeout' => 'required|integer|min:1000|max:30000',
                'snmp_retries' => 'required|integer|min:1|max:10',
                'polling_interval' => 'required|integer|min:60|max:3600',
                'alert_threshold_cpu' => 'required|integer|min:50|max:100',
                'alert_threshold_memory' => 'required|integer|min:50|max:100',
                'alert_threshold_temperature' => 'required|integer|min:40|max:100',
                'enable_auto_discovery' => 'boolean',
                'enable_alerts' => 'boolean',
            ]);

            // Save settings
            foreach ($settings as $key => $value) {
                setting()->set('fiberhome_' . $key, $value);
            }

            // Save boolean settings
            setting()->set('fiberhome_enable_auto_discovery', $request->has('enable_auto_discovery'));
            setting()->set('fiberhome_enable_alerts', $request->has('enable_alerts'));

            return $response
                ->setMessage(trans('plugins/fiberhome-olt-manager::settings.updated_success'));
        } catch (\Exception $e) {
            return $response
                ->setError()
                ->setMessage(trans('plugins/fiberhome-olt-manager::settings.updated_error') . ': ' . $e->getMessage());
        }
    }
}