<div>
    <button type="button"
        class="{{ $class ?? 'btn btn-icon btn-active-light-warning w-30px h-30px me-3' }} toggle-status-btn"
        data-url="{{ $action }}" data-id="{{ $id }}"
        title="{{ $isActive ? 'Deactivate Article' : 'Activate Article' }}">
        @if ($isActive)
            <i class="ki-duotone ki-eye fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
            </i>
        @else
            <i class="ki-duotone ki-eye-slash fs-2">
                <span class="path1"></span>
                <span class="path2"></span>
                <span class="path3"></span>
                <span class="path4"></span>
            </i>
        @endif
    </button>
</div>
