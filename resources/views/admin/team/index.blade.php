@extends('layouts.admin')

@section('title', 'Manage Team')
@section('page-title', 'Team')
@section('page-subtitle', 'Introduce the people behind Devgenfour')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#teamModal">Add member</button>
</div>
<table class="table table-striped" id="team-table" style="width:100%">
    <thead>
        <tr>
            <th>Name</th>
            <th>Role</th>
            <th>Order</th>
            <th>Visible</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="teamModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="teamForm" method="POST" action="{{ route('admin.teams.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Team member</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Role</label>
                            <input type="text" name="role_title" class="form-control" required>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Bio</label>
                            <textarea name="bio" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Order</label>
                            <input type="number" name="order_index" class="form-control" value="0" min="0">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                        </div>
                        <div class="col-12">
                            <h6 class="text-secondary">Social links</h6>
                            @for($i = 0; $i < 3; $i++)
                                <div class="row g-2 mb-2 social-group">
                                    <div class="col-md-4">
                                        <input type="text" name="social_links[{{ $i }}][label]" class="form-control" placeholder="Label (LinkedIn)">
                                    </div>
                                    <div class="col-md-8">
                                        <input type="url" name="social_links[{{ $i }}][url]" class="form-control" placeholder="https://">
                                    </div>
                                </div>
                            @endfor
                        </div>
                        <div class="col-12">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="memberVisible" name="is_visible" checked>
                                <label class="form-check-label" for="memberVisible">Visible on site</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save member</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const table = $('#team-table').DataTable({
            ajax: '{{ route('admin.teams.index') }}',
            serverSide: true,
            processing: true,
            columns: [
                { data: 'name', name: 'name' },
                { data: 'role_title', name: 'role_title' },
                { data: 'order_index', name: 'order_index' },
                { data: 'is_visible', render: data => data ? '<span class="badge bg-success">Visible</span>' : '<span class="badge bg-secondary">Hidden</span>' },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function (_, __, row) {
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

        const modal = document.getElementById('teamModal');
        modal.addEventListener('show.bs.modal', function () {
            const form = document.getElementById('teamForm');
            form.reset();
            form.action = '{{ route('admin.teams.store') }}';
            form.querySelector('input[name="_method"]')?.remove();
            Array.from(form.querySelectorAll('.social-group')).forEach(group => {
                group.querySelectorAll('input').forEach(input => input.value = '');
            });
            form.querySelector('[name="is_visible"]').checked = true;
        });

        $('#teamForm').on('submit', function (event) {
            event.preventDefault();
            Devgenfour.submitForm(this, table);
        });

        $('#team-table').on('click', 'button[data-action="edit"]', function () {
            const rowData = table.row($(this).closest('tr')).data();
            const form = document.getElementById('teamForm');
            const modalInstance = new bootstrap.Modal(modal);
            form.reset();
            form.action = `{{ url('/teams') }}/${rowData.id}`;
            form.querySelector('input[name="_method"]')?.remove();
            form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
            form.querySelector('[name="name"]').value = rowData.name;
            form.querySelector('[name="role_title"]').value = rowData.role_title;
            form.querySelector('[name="bio"]').value = rowData.bio || '';
            form.querySelector('[name="order_index"]').value = rowData.order_index;
            form.querySelector('[name="is_visible"]').checked = rowData.is_visible;
            const socialGroups = form.querySelectorAll('.social-group');
            (rowData.social_links || []).forEach((link, index) => {
                if (socialGroups[index]) {
                    socialGroups[index].querySelector('[name^="social_links"][name$="[label]"]').value = link.label || '';
                    socialGroups[index].querySelector('[name^="social_links"][name$="[url]"]').value = link.url || '';
                }
            });
            modalInstance.show();
        });

        $('#team-table').on('click', 'button[data-action="delete"]', function () {
            if (!confirm('Delete this team member?')) { return; }
            const id = this.dataset.id;
            fetch(`{{ url('/teams') }}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => table.ajax.reload());
        });
    });
</script>
@endpush
