@extends('layouts.admin')

@section('title', 'Contact Messages')
@section('page-title', 'Contact inbox')
@section('page-subtitle', 'Track new leads and respond quickly')

@section('content')
<table class="table table-striped" id="contacts-table" style="width:100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Email</th>
            <th>Status</th>
            <th>Received</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="offcanvas offcanvas-end" tabindex="-1" id="contactCanvas" aria-labelledby="contactCanvasLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="contactCanvasLabel">Message</h5>
        <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <dl class="row small">
            <dt class="col-4 text-secondary">Name</dt>
            <dd class="col-8" id="contactName">—</dd>
            <dt class="col-4 text-secondary">Email</dt>
            <dd class="col-8" id="contactEmail">—</dd>
            <dt class="col-4 text-secondary">Company</dt>
            <dd class="col-8" id="contactCompany">—</dd>
            <dt class="col-4 text-secondary">Phone</dt>
            <dd class="col-8" id="contactPhone">—</dd>
            <dt class="col-4 text-secondary">Status</dt>
            <dd class="col-8" id="contactStatus">—</dd>
        </dl>
        <div class="mb-3">
            <h6 class="text-secondary">Message</h6>
            <p id="contactMessage">—</p>
        </div>
        <div class="d-flex gap-2">
            <button class="btn btn-success" id="markHandled">Mark handled</button>
            <button class="btn btn-outline-danger" id="deleteMessage">Delete</button>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = $('#contacts-table').DataTable({
            ajax: '{{ route('admin.contacts.index') }}',
            serverSide: true,
            processing: true,
            columns: [
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'status', render: data => data === 'new' ? '<span class="badge bg-warning text-dark">New</span>' : '<span class="badge bg-success">Handled</span>' },
                { data: 'created_at', name: 'created_at' },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function (_, __, row) {
                        return `<div class="table-actions">
                            <button class="btn btn-sm btn-outline-primary" data-action="view" data-id="${row.id}">View</button>
                        </div>`;
                    }
                }
            ]
        });

        const canvasElement = document.getElementById('contactCanvas');
        const offcanvas = new bootstrap.Offcanvas(canvasElement);
        let currentId = null;

        $('#contacts-table').on('click', 'button[data-action="view"]', function () {
            currentId = this.dataset.id;
            fetch(`{{ url('/contacts') }}/${currentId}`)
                .then(response => response.json())
                .then(data => {
                    document.getElementById('contactName').innerText = data.name;
                    document.getElementById('contactEmail').innerText = data.email;
                    document.getElementById('contactCompany').innerText = data.company || '—';
                    document.getElementById('contactPhone').innerText = data.phone || '—';
                    document.getElementById('contactStatus').innerText = data.status;
                    document.getElementById('contactMessage').innerText = data.message;
                    offcanvas.show();
                });
        });

        document.getElementById('markHandled').addEventListener('click', function () {
            if (!currentId) return;
            fetch(`{{ url('/contacts') }}/${currentId}/read`, {
                method: 'PATCH',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                offcanvas.hide();
                table.ajax.reload(null, false);
                Devgenfour.flash('Message marked as handled');
            });
        });

        document.getElementById('deleteMessage').addEventListener('click', function () {
            if (!currentId || !confirm('Delete this message?')) return;
            fetch(`{{ url('/contacts') }}/${currentId}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                offcanvas.hide();
                table.ajax.reload(null, false);
                Devgenfour.flash('Message deleted');
            });
        });
    });
</script>
@endpush
