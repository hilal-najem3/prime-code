<x-form.table-paginated :name="'Service Orders'" :collection="$orders" :columns="[
        ['label' => 'ID', 'field' => 'id'],
        ['label' => 'Service', 'field' => 'service.name'],
        ['label' => 'Employee', 'field' => 'user.name'],
        ['label' => 'Customer', 'field' => 'customer.name'],
        ['label' => 'Status', 'field' => 'status'],
        ['label' => 'Payment', 'field' => 'payment_status'],
        ['label' => 'Created', 'field' => 'created_at']
    ]" :routes="[
        'create' => 'admin.service-orders.create',
        'edit' => 'admin.service-orders.edit',
        'delete' => 'admin.service-orders.destroy'
    ]" />