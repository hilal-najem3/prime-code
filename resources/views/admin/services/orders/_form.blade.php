@php
$isEdit = isset($order);
@endphp

<form method="POST"
    action="{{ $isEdit ? route('admin.service-orders.update', $order->id) : route('admin.service-orders.store') }}">
    @csrf
    @if ($errors->any())
    @foreach ($errors->all() as $error)
    <x-ui.toast :message="$error" :color="'red'" />
    @endforeach
    @endif

    @if($isEdit)
    @method('PUT')
    @endif

    {{-- Service --}}
    <x-form.select name="service_id" label="Service" :options="$services"
        :selected="old('service_id', $order->service_id ?? '')" required />

    {{-- Admin --}}
    <x-form.select name="employee_id" label="Employee" :options="$admins->pluck('name', 'id')"
        :selected="old('employee_id', $order->employee_id ?? '')" required />

    {{-- User --}}
    <x-form.select name="customer_id" label="Customer" :options="$users->pluck('name', 'id')"
        :selected="old('customer_id', $order->customer_id ?? '')" required />

    <x-form.price name="price" currenciesName="currency_id" :currencies="$currencies"
        :value="old('price', $service->price ?? 0)" :required="true" label="Price" />

    {{-- Status --}}
    <x-form.select name="status" label="Order Status"
        :options="['pending' => 'Pending','approved' => 'Approved','processing' => 'Processing','completed' => 'Completed','rejected' => 'Rejected']"
        :selected="old('status', $order->status ?? '')" required />

    {{-- Payment Status --}}
    <x-form.select name="payment_status" label="Payment Status" :options="['unpaid' => 'Unpaid', 'paid' => 'Paid']"
        :selected="old('payment_status', $order->payment_status ?? '')" required />

    {{-- Note --}}
    <x-form.translatable-editor-table label="Order Note" name="note" :languages="$languages"
        :value="old('note', $order?->getTranslations('note') ?? [])" />

    <div class="flex justify-between pt-2">
        <a href="{{ route('admin.service-orders.index') }}" class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400">←
            Back</a>
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">
            {{ $isEdit ? 'Update Order' : 'Create Order' }}
        </button>
    </div>
</form>