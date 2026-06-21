{{--
    Reusable delete confirmation modal.

    Props:
      id            - unique DOM id for this modal instance (default: 'deleteConfirmModal')
      title         - heading text (default: 'Delete this item?')
      message       - body text shown before the bolded detail (default: 'You are about to delete this item.')
      confirmId     - id for the confirm/delete button (default: '{id}ConfirmBtn')
      cancelId      - id for the cancel button (default: '{id}CancelBtn')
      closeId       - id for the (x) close button (default: '{id}CloseBtn')

    Usage (single item):
        <x-delete-confirm-modal
            id="deleteConfirmModal"
            title="Delete Category?"
            message="You are about to delete this category. This action cannot be undone."
        />

    Usage (bulk):
        <x-delete-confirm-modal
            id="bulkDeleteConfirmModal"
            title="Delete Selected Categories?"
        >
            You are about to delete <strong><span id="bulkDeleteCountText">0</span> categor<span id="bulkDeleteCountWord">y</span></strong>. This action cannot be undone.
        </x-delete-confirm-modal>
--}}

@props([
    'id' => 'deleteConfirmModal',
    'title' => 'Delete this item?',
    'message' => 'You are about to delete this item. This action cannot be undone.',
    'confirmId' => null,
    'cancelId' => null,
    'closeId' => null,
])

@php
    $confirmId = $confirmId ?? $id . 'ConfirmBtn';
    $cancelId = $cancelId ?? $id . 'CancelBtn';
    $closeId = $closeId ?? $id . 'CloseBtn';
@endphp

<div id="{{ $id }}" class="delete-confirm-modal">
    <div class="delete-modal-box">
        <button type="button" class="delete-modal-close" id="{{ $closeId }}" title="Close">
            <i class="fas fa-times"></i>
        </button>
        <div class="delete-modal-icon">
            <i class="fas fa-triangle-exclamation"></i>
        </div>
        <h3>{{ $title }}</h3>
        <p>
            {{ $slot->isEmpty() ? $message : $slot }}
        </p>
        <div class="delete-modal-actions">
            <button type="button" class="delete-btn-cancel" id="{{ $cancelId }}">
                <i class="fas fa-xmark"></i> Cancel
            </button>
            <button type="button" class="delete-btn-confirm" id="{{ $confirmId }}">
                <i class="fas fa-trash"></i> Delete
            </button>
        </div>
    </div>
</div>