@props([
    'icon',
    'color',
    'title',
    'subtitle',
    'tooltip',
    'value_id',
    'percent_id' => null,
    'progress_id' => null,
    'show_menu' => false,
    'export_types' => null,
])
<div class="card border-0 shadow-sm h-100">
    <div class="card-body text-center position-relative">
        @if ($show_menu)
            <div class="card-menu position-absolute top-0 end-0 mt-2 me-2">
                <button class="card-menu-toggle" type="button" onclick="toggleCardMenu(this)">
                    <i class="fas fa-ellipsis-v"></i>
                </button>
                <div class="card-menu-dropdown">
                    <button class="card-menu-item" onclick="exportMembershipStats('{{ $export_types }}')">
                        <i class="fas fa-file-excel"></i>
                        Download Excel
                    </button>
                </div>
            </div>
        @endif

        <div class="bg-{{ $color }}
                        bg-opacity-10 rounded-circle p-3 mx-auto mb-3"
            style="width: 70px; height: 70px; display: flex; align-items: center; justify-content: center;">
            <i class="{{ $icon }} text-{{ $color }} fs-3"></i>
        </div>
        <h5 class="text-muted mb-2">
            {{ $title }}
            <div class="info-icon" data-bs-toggle="tooltip" data-bs-placement="top" title="{{ $tooltip }}">
                <i class="fas fa-info"></i>
            </div>
        </h5>
        <h3 class="fw-bold text-{{ $color }} mb-1"><span id="{{ $value_id }}">0</span></h3>
        @if ($subtitle)
            <h6 class="text-muted mb-2">{{ $subtitle }}</h6>
        @endif
        @if ($percent_id && $progress_id)
            <small class="text-{{ $color }}"><span id="{{ $percent_id }}">0</span>% of member</small>
            <div class="progress mt-2" style="height: 10px;">
                <div id="{{ $progress_id }}" class="progress-bar bg-{{ $color }}" style="width: 0%">
                </div>
            </div>
        @endif
    </div>
</div>
