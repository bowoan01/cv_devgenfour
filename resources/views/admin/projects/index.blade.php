@extends('layouts.admin')

@section('title', 'Manage Projects')
@section('page-title', 'Projects')
@section('page-subtitle', 'Publish portfolio case studies and manage assets')

@section('content')
<div class="d-flex justify-content-end mb-3">
    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#projectModal" data-mode="create">Add project</button>
</div>
<table class="table table-striped" id="projects-table" style="width:100%">
    <thead>
        <tr>
            <th>Title</th>
            <th>Category</th>
            <th>Client</th>
            <th>Published</th>
            <th>Updated</th>
            <th>Actions</th>
        </tr>
    </thead>
</table>

<div class="modal fade" id="projectModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
        <div class="modal-content">
            <form id="projectForm" method="POST" action="{{ route('admin.projects.store') }}" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title">Project</h5>
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
                        <div class="col-md-6">
                            <label class="form-label">Client</label>
                            <input type="text" name="client" class="form-control">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Category</label>
                            <input type="text" name="category" class="form-control">
                        </div>
                        <div class="col-12">
                            <label class="form-label">Summary</label>
                            <textarea name="summary" class="form-control" rows="3"></textarea>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Results / Narrative</label>
                            <textarea name="results" class="form-control" rows="4"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tech stack (comma separated)</label>
                            <input type="text" name="tech_stack" class="form-control" placeholder="Laravel, React, AWS">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Cover image</label>
                            <input type="file" name="cover_image" class="form-control" accept="image/*">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tags</label>
                            <select name="tags[]" class="form-select" multiple>
                                @foreach($tags as $tag)
                                    <option value="{{ $tag->id }}">{{ $tag->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 d-flex align-items-end">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" value="1" id="projectPublished" name="is_published">
                                <label class="form-check-label" for="projectPublished">
                                    Published
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Save project</button>
                </div>
            </form>
        </div>
    </div>
</div>

<div class="modal fade" id="galleryModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Project gallery</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="galleryForm" class="mb-3" enctype="multipart/form-data">
                    @csrf
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Upload image</label>
                            <input type="file" name="image" class="form-control" accept="image/*" required>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">Caption</label>
                            <input type="text" name="caption" class="form-control">
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary mt-3">Add to gallery</button>
                </form>
                <div id="galleryList" class="row g-3"></div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const storageBase = @json(asset('storage'));
        const table = $('#projects-table').DataTable({
            ajax: '{{ route('admin.projects.index') }}',
            serverSide: true,
            processing: true,
            columns: [
                { data: 'title', name: 'title' },
                { data: 'category', name: 'category' },
                { data: 'client', name: 'client' },
                { data: 'is_published', render: data => data ? '<span class="badge bg-success">Published</span>' : '<span class="badge bg-secondary">Draft</span>' },
                { data: 'updated_at', name: 'updated_at' },
                {
                    data: 'actions',
                    orderable: false,
                    searchable: false,
                    render: function (_, __, row) {
                        return `
                            <div class="table-actions">
                                <button class="btn btn-sm btn-outline-primary" data-action="edit" data-id="${row.id}">Edit</button>
                                <button class="btn btn-sm btn-outline-info" data-action="gallery" data-id="${row.id}">Gallery</button>
                                <button class="btn btn-sm btn-outline-danger" data-action="delete" data-id="${row.id}">Delete</button>
                            </div>
                        `;
                    }
                }
            ]
        });

        const projectModal = document.getElementById('projectModal');
        projectModal.addEventListener('show.bs.modal', function () {
            const form = document.getElementById('projectForm');
            form.reset();
            form.action = '{{ route('admin.projects.store') }}';
            form.querySelector('input[name="_method"]')?.remove();
            const tagsSelect = form.querySelector('[name="tags[]"]');
            Array.from(tagsSelect.options).forEach(option => option.selected = false);
            form.querySelector('[name="is_published"]').checked = false;
        });

        $('#projectForm').on('submit', function (event) {
            event.preventDefault();
            Devgenfour.submitForm(this, table);
        });

        $('#projects-table').on('click', 'button[data-action="edit"]', function () {
            const rowData = table.row($(this).closest('tr')).data();
            const form = document.getElementById('projectForm');
            const modalInstance = new bootstrap.Modal(projectModal);
            form.reset();
            form.action = `{{ url('/projects') }}/${rowData.id}`;
            form.querySelector('input[name="_method"]')?.remove();
            form.insertAdjacentHTML('afterbegin', '<input type="hidden" name="_method" value="PUT">');
            form.querySelector('[name="title"]').value = rowData.title;
            form.querySelector('[name="slug"]').value = rowData.slug;
            form.querySelector('[name="client"]').value = rowData.client || '';
            form.querySelector('[name="category"]').value = rowData.category || '';
            form.querySelector('[name="summary"]').value = rowData.summary || '';
            form.querySelector('[name="results"]').value = rowData.results || '';
            form.querySelector('[name="tech_stack"]').value = (rowData.tech_stack || []).join(', ');
            form.querySelector('[name="is_published"]').checked = rowData.is_published;
            const tagsSelect = form.querySelector('[name="tags[]"]');
            Array.from(tagsSelect.options).forEach(option => {
                option.selected = (rowData.tag_ids || []).includes(Number(option.value));
            });
            modalInstance.show();
        });

        $('#projects-table').on('click', 'button[data-action="delete"]', function () {
            if (!confirm('Delete this project?')) { return; }
            const id = this.dataset.id;
            fetch(`{{ url('/projects') }}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => table.ajax.reload());
        });

        const galleryModal = document.getElementById('galleryModal');
        let currentProjectId = null;
        let currentImages = [];
        let sortableInstance = null;

        $('#projects-table').on('click', 'button[data-action="gallery"]', function () {
            const rowData = table.row($(this).closest('tr')).data();
            currentProjectId = rowData.id;
            currentImages = rowData.images || [];
            renderGallery();
            const modalInstance = new bootstrap.Modal(galleryModal);
            modalInstance.show();
        });

        function renderGallery() {
            const container = document.getElementById('galleryList');
            container.innerHTML = '';
            currentImages.forEach(image => {
                const col = document.createElement('div');
                col.classList.add('col-md-6');
                col.innerHTML = `
                    <div class="card h-100">
                        <img src="${storageBase}/${image.path}" class="card-img-top" alt="${image.caption || ''}">
                        <div class="card-body">
                            <p class="small text-secondary mb-2">${image.caption || ''}</p>
                            <button class="btn btn-sm btn-outline-danger" data-delete="${image.id}">Remove</button>
                        </div>
                    </div>
                `;
                container.appendChild(col);
            });
            if (sortableInstance) {
                sortableInstance.destroy();
            }
            sortableInstance = Sortable.create(container, {
                animation: 150,
                onEnd: function () {
                    const order = {};
                    container.querySelectorAll('[data-delete]').forEach((button, index) => {
                        order[button.dataset.delete] = index + 1;
                    });
                    fetch(`{{ url('/projects') }}/${currentProjectId}/reorder`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: JSON.stringify({ order })
                    }).then(() => {
                        currentImages.sort((a, b) => order[a.id] - order[b.id]);
                    });
                }
            });
        }

        $('#galleryForm').on('submit', function (event) {
            event.preventDefault();
            const formData = new FormData(this);
            fetch(`{{ url('/projects') }}/${currentProjectId}/images`, {
                method: 'POST',
                body: formData,
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
                .then(response => response.json())
                .then((payload) => {
                    if (payload.data) {
                        currentImages.push(payload.data);
                        renderGallery();
                    }
                    table.ajax.reload(null, false);
                    this.reset();
                });
        });

        $('#galleryList').on('click', '[data-delete]', function () {
            if (!confirm('Delete this image?')) { return; }
            const id = this.dataset.delete;
            fetch(`{{ url('/projects/images') }}/${id}`, {
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
            }).then(() => {
                currentImages = currentImages.filter(image => String(image.id) !== String(id));
                renderGallery();
                table.ajax.reload(null, false);
            });
        });
    });
</script>
@endpush
