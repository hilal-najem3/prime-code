<?php

namespace App\Http\Controllers\Admin\Services;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\ServiceOrder as Order;
use App\Models\Service;
use App\Models\Currency;
use App\Models\Language;
use Illuminate\Support\Facades\DB;
use Exception;
use App\Http\Requests\Services\Orders\CreateServiceOrderRequest;
use App\Http\Requests\Services\Orders\UpdateServiceOrderRequest;
use App\Models\User;
use App\Models\Role;

class ServiceOrdersController extends Controller
{
    public function index(Request $request)
    {
        $orders = Order::with(['user', 'service', 'currency'])
            ->when($request->filled('status'), fn($q) => $q->where('status', $request->status))
            ->when($request->filled('payment_status'), fn($q) => $q->where('payment_status', $request->payment_status))
            ->latest()
            ->paginate(10);

        if ($request->ajax()) {
            return view('admin.services.orders._table', compact('orders'))->render();
        }

        return view('admin.services.orders.index', compact('orders'));
    }

    public function create()
    {
        $services = Service::all()->mapWithKeys(function ($service) {
            return [$service->id => $service->localized_name];
        });
        $currencies = Currency::all();
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            abort(404, 'Admin role not found');
        }
        $admins = $adminRole->users()
            ->where('active', true)
            ->get();
        $role = Role::where('name', 'user')->first();
        if (!$role) {
            abort(404, 'User role not found');
        }
        $users = $role->users()
            ->get();
        $languages = Language::where('is_active', true)
            ->get();

        return view('admin.services.orders.create', compact(
            'services',
            'currencies',
            'users',
            'admins',
            'languages'
        ));
    }

    public function store(CreateServiceOrderRequest $request)
    {
        try {
            DB::beginTransaction();

            $order = Order::create($request->validated());

            DB::commit();
            return redirect()->route('admin.service-orders.index')
                ->with('success', 'Service order created successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function show(string $id)
    {
        $order = Order::with(['user', 'service', 'currency'])->findOrFail($id);
        return view('admin.services.orders.show', compact('order'));
    }

    public function edit(string $id)
    {
        $order = Order::findOrFail($id);
        $services = Service::all()->mapWithKeys(function ($service) {
            return [$service->id => $service->localized_name];
        });
        $currencies = Currency::all();
        $adminRole = Role::where('name', 'admin')->first();
        if (!$adminRole) {
            abort(404, 'Admin role not found');
        }
        $admins = $adminRole->users()
            ->where('active', true)
            ->get();
        $role = Role::where('name', 'user')->first();
        if (!$role) {
            abort(404, 'User role not found');
        }
        $users = $role->users()
            ->get();
        $languages = Language::where('is_active', true)
            ->get();


        return view('admin.services.orders.edit', compact('order', 'services', 'currencies', 'users', 'admins', 'languages'));
    }

    public function update(UpdateServiceOrderRequest $request, int $id)
    {
        try {
            DB::beginTransaction();

            $order = Order::findOrFail($id);
            $order->update($request->validated());

            DB::commit();
            return redirect()->route('admin.service-orders.index')
                ->with('success', 'Service order updated successfully.');
        } catch (Exception $e) {
            DB::rollBack();
            return back()->withErrors(['error' => $e->getMessage()])->withInput();
        }
    }

    public function destroy(int $id)
    {
        try {
            $order = Order::findOrFail($id);
            $order->delete();

            return redirect()->route('admin.service-orders.index')
                ->with('success', 'Service order deleted successfully.');
        } catch (Exception $e) {
            return back()->withErrors(['error' => $e->getMessage()]);
        }
    }
}