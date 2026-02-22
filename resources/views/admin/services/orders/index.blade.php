@extends('layouts.admin')

@section('content')
<div>
    <div class="flex gap-4 mb-4">
        <select id="statusFilter" class="form-select">
            <option value="">All Statuses</option>
            <option value="pending">Pending</option>
            <option value="processing">Processing</option>
            <option value="approved">Approved</option>
            <option value="rejected">Rejected</option>
            <option value="completed">Completed</option>
        </select>

        <select id="paymentStatusFilter" class="form-select">
            <option value="">All Payment Statuses</option>
            <option value="unpaid">Unpaid</option>
            <option value="paid">Paid</option>
            <option value="failed">Failed</option>
        </select>
    </div>

    <div id="orders-table-container">
        @include('admin.services.orders._table', ['orders' => $orders])
    </div>
</div>
@endsection

@push('scripts')
<script>
    function loadOrders(page = 1) {
        const status = document.getElementById('statusFilter').value;
        const paymentStatus = document.getElementById('paymentStatusFilter').value;

        const params = new URLSearchParams({
            page,
            status,
            payment_status: paymentStatus,
        });

        fetch("{{ route('admin.service-orders.index') }}?" + params.toString(), {
            headers: { 'X-Requested-With': 'XMLHttpRequest' }
        })
        .then(res => res.text())
        .then(html => {
            document.getElementById('orders-table-container').innerHTML = html;
        });
    }

    document.getElementById('statusFilter').addEventListener('change', () => loadOrders());
    document.getElementById('paymentStatusFilter').addEventListener('change', () => loadOrders());

    // Delegate pagination link clicks
    document.addEventListener('click', function(e) {
        if (e.target.matches('#orders-table-container .pagination a')) {
            e.preventDefault();
            const url = new URL(e.target.href);
            const page = url.searchParams.get('page');
            loadOrders(page);
        }
    });
</script>
@endpush