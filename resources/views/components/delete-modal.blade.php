<div id="deleteConfirmModal" class="modal">
    <div class="delete-modal-box">
        <div class="delete-modal-icon"><i class="fas fa-trash-alt"></i></div>
        <h3>{{ $title ?? 'Delete Record?' }}</h3>
        <p>This action <strong>cannot be undone.</strong> {{ $message ?? 'Are you sure you want to permanently delete this record?' }}</p>
        <form id="deleteForm" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="delete-modal-actions">
                <button type="button" id="deleteModalClose" class="delete-btn-cancel">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button type="submit" id="confirmDeleteBtn" class="delete-btn-confirm">
                    <i class="fas fa-trash-alt"></i> Yes, Delete
                </button>
            </div>
        </form>
    </div>
</div>