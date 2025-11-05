@extends('layouts.admin')

@section('title', 'Manage Services')
@section('page-title', 'Services')
@section('page-subtitle', 'Curate the offerings displayed on the public site')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#serviceModal" data-action="create">Add service</button>
</div>
<table class="table table-striped" id="services-table" style="width:100%">
    <thead>
        <tr>
            <th>Title</th>
            <th>Slug</th>
            <th>Order</th>
            <th>Published</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="serviceModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="serviceForm" method="POST" action="{{ route('admin.services.store') }}">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Service</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Title</label>
                            <input type="text" name="title" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Slug</label>
                            <input type="text" name="slug" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Short description</label>
                            <input type="text" name="short_description" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Description</label>
                            <textarea name="description" class="form-control" rows="5"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Display order</label>
                            <input type="number" name="display_order" class="form-control" min="0" value="0">
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="servicePublished" name="is_published">
                                <label class="form-check-label" for="servicePublished">
                                    Published
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save service</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = $('#services-table').DataTable({
            ajax: '{{ route('admin.services.index') }}',
            serverSide: true,
            processing: true,
            columns: [
                { data: 'title', name: 'title' },
                { data: 'slug', name: 'slug' },
                { data: 'display_order', name: 'display_order' },
                { data: 'is_published', render: data => data ? '<span class="badge bg-success">Yes</span>' : '<span class="badge bg-secondary">No</span>' },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function (data, type, row) {
                        return `
                            <div class="table-actions">
                                <button class="btn btn-sm btn-outline-primary" data-action="edit" data-id="${row.id}">Edit</button>
                                <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${row.id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ]
        });

        const modal = document.getElementById('serviceModal');
        modal.addEventListener('show.bs.modal', function () {
            const form = document.getElementById('serviceForm');
            form.reset();
            form.action = '{{ route('admin.services.store') }}';
            form.querySelector('input[name="_method"]')?.remove();
            form.querySelector('[name="display_order"]').value = 0;
            form.querySelector('[name="is_published"]').checked = false;
        });

        $('#serviceForm').on('submit', function (event) {
            event.preventDefault();
            Devgenfour.submitForm(this, table);
        });

        $('#services-table').on('click', 'button[data-action="edit"]', function () {
            const rowData = table.row($(this).closest('tr')).data();
            const modalElement = document.getElementById('serviceModal');
            const form = document.getElementById('serviceForm');
            const modalInstance = new bootstrap.Modal(modalElement);
            form.reset();
            form.action = `{{ url('/services') }}/${rowData.id}`;
            form.querySelector('input[name="_method"]')?.remove();
            form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
            form.querySelector('[name="title"]').value = rowData.title;
            form.querySelector('[name="slug"]').value = rowData.slug;
            form.querySelector('[name="short_description"]').value = rowData.short_description || '';
            form.querySelector('[name="description"]').value = rowData.description || '';
            form.querySelector('[name="display_order"]').value = rowData.display_order;
            form.querySelector('[name="is_published"]').checked = rowData.is_published;
            modalInstance.show();
        });

        $('#services-table').on('click', 'button[data-action="delete"]', function () {
            if (!confirm('Delete this service?')) { return; }
            const id = this.dataset.id;
            fetch(`{{ url('/services') }}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => table.ajax.reload());
        });
    });
</script>
@endpush
