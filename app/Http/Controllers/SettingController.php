<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::orderBy('group')->get()->groupBy('group');
        return view('settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->except('_token', '_method');

        if ($request->hasFile('store_logo')) {
            $path = $request->file('store_logo')->store('logo', 'public');
            Setting::updateOrCreate(['key' => 'store_logo'], ['value' => $path, 'group' => 'store']);
        }

        foreach ($data as $key => $value) {
            if (in_array($key, ['store_logo'])) continue;

            $type = 'string';
            $group = 'general';
            if (str_starts_with($key, 'store_')) $group = 'store';
            elseif (str_starts_with($key, 'receipt_')) $group = 'receipt';
            elseif (str_starts_with($key, 'tax_')) $group = 'payment';

            if (is_bool($value) || $value === '1' || $value === '0') $type = 'boolean';
            elseif (is_numeric($value)) $type = 'integer';

            Setting::updateOrCreate(
                ['key' => $key],
                ['value' => is_array($value) ? json_encode($value) : $value, 'type' => $type, 'group' => $group]
            );
        }

        // Handle checkbox yang tidak terkirim
        foreach (['receipt_show_logo','receipt_show_address','receipt_show_phone','receipt_show_cashier','receipt_show_customer','receipt_show_discount','receipt_show_tax','tax_enabled'] as $chk) {
            if (!$request->has($chk)) {
                Setting::updateOrCreate(['key' => $chk], ['value' => '0', 'type' => 'boolean']);
            }
        }

        cache()->forget('app_settings');
        AuditLog::record('Mengubah pengaturan', 'setting');
        return back()->with('success', 'Pengaturan berhasil disimpan.');
    }
}