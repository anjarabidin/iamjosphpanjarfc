@php
$currentMonth = now()->format('m');
$currentYear = now()->format('Y');
$inputId = $inputId;
@endphp

@push('css')
<style>
    .z-50 {
        z-index: 50;
    }
    .w-64 {
        width: 18rem;
    }
    .text-lg {
        font-size: 1.125rem;
    }
    .active-month {
        background-color: var(--bs-primary) !important;
        color: white !important;
    }
    .text-base {
        font-size: 1rem;
    }
</style>
@endpush

<div class="position-relative monthpicker z-50">
    <div id="{{ $inputId }}-toggle" class="form-select form-select-sm cursor-pointer d-flex align-items-center">
        <span id="{{ $inputId }}-display" class="me-2">{{ $currentYear }}-{{ $currentMonth }}</span>
    </div>

    <input type="hidden" id="{{ $inputId }}" name="monthpicker" value="{{ $currentYear }}-{{ $currentMonth }}" class="position-absolute w-100 h-100 opacity-0 cursor-pointer">

    <div id="{{ $inputId }}-popover" class="position-absolute z-10 end-0 mt-2 w-64 bg-light border rounded rounded-3 shadow-lg p-4" style="display: none;">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <button id="{{ $inputId }}-year-prev" class="btn btn-outline-secondary py-1 px-2 rounded rounded-3">
                <img src="{{ asset('/assets/media/icons/arrow-left.svg') }}" alt="arrow-left" style="width: 1.5rem; height: 1.5rem;">
            </button>
            <span id="{{ $inputId }}-year-display" class="fw-semibold text-dark text-lg">{{ $currentYear }}</span>
            <button id="{{ $inputId }}-year-next" class="btn btn-outline-secondary py-1 px-2 rounded rounded-3">
                <img src="{{ asset('/assets/media/icons/arrow-right.svg') }}" alt="arrow-right" class="rotate-180" style="width: 1.5rem; height: 1.5rem;">
            </button>
        </div>
        <div class="row row-cols-3 g-2">
            @foreach (['01' => 'Jan', '02' => 'Feb', '03' => 'Mar', '04' => 'Apr', '05' => 'Mei', '06' => 'Jun', '07' => 'Jul', '08' => 'Agu', '09' => 'Sep', '10' => 'Okt', '11' => 'Nov', '12' => 'Des'] as $num => $name)
                <button class="col btn text-dark rounded-3 py-1 month-button shadow-none text-base" data-month="{{ $num }}">
                    {{ $name }}
                </button>
            @endforeach
            <button id="{{ $inputId }}-this-month" class="btn text-end font-medium text-base col-12 text-primary mt-3 pb-1">
                This Month
            </button>
        </div>
    </div>
</div>

@push('js')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const toggle = document.getElementById('{{ $inputId }}-toggle');
        const popover = document.getElementById('{{ $inputId }}-popover');
        const display = document.getElementById('{{ $inputId }}-display');
        const input = document.getElementById('{{ $inputId }}');
        const monthButtons = popover.querySelectorAll('.month-button[data-month]');
        const thisMonthButton = document.getElementById('{{ $inputId }}-this-month');
        const yearDisplay = document.getElementById('{{ $inputId }}-year-display');
        const yearPrev = document.getElementById('{{ $inputId }}-year-prev');
        const yearNext = document.getElementById('{{ $inputId }}-year-next');

        let currentYear = {{ $currentYear }};
        let currentMonth = '{{ $currentMonth }}';

        const monthNames = {
            '01': 'Jan',
            '02': 'Feb',
            '03': 'Mar',
            '04': 'Apr',
            '05': 'Mei',
            '06': 'Jun',
            '07': 'Jul',
            '08': 'Agu',
            '09': 'Sep',
            '10': 'Okt',
            '11': 'Nov',
            '12': 'Des'
        };

        updateDisplay();

        toggle.addEventListener('click', function(e) {
            e.preventDefault();
            popover.style.display = popover.style.display === 'none' ? 'block' : 'none';
        });

        document.addEventListener('click', function(event) {
            if (!event.target.closest('.monthpicker')) {
                popover.style.display = 'none';
            }
        });

        monthButtons.forEach(button => {
            button.addEventListener('click', function() {
                currentMonth = this.dataset.month;
                updateDisplay();
                setActiveButton();
                popover.style.display = 'none';
                dispatchChangeEvent();
            });
        });

        thisMonthButton.addEventListener('click', function() {
            currentYear = {{ now()->format('Y') }};
            currentMonth = '{{ now()->format('m') }}';
            updateDisplay();
            setActiveButton();
            popover.style.display = 'none';
            dispatchChangeEvent();
        });

        yearPrev.addEventListener('click', function() {
            currentYear--;
            updateYearDisplay();
        });

        yearNext.addEventListener('click', function() {
            currentYear++;
            updateYearDisplay();
        });

        function updateDisplay() {
            const displayMonth = monthNames[currentMonth];
            display.textContent = `${displayMonth}, ${currentYear}`;
            input.value = `${currentYear}-${currentMonth}`;
        }

        function updateYearDisplay() {
            yearDisplay.textContent = currentYear;
            updateDisplay();
            dispatchChangeEvent();
        }

        function setActiveButton() {
            monthButtons.forEach(button => {
                button.classList.remove('active-month');
                if (button.dataset.month === currentMonth) {
                    button.classList.add('active-month');
                }
            });
        }

        function dispatchChangeEvent() {
            const monthPickerId = input.getAttribute('id');
            const event = new CustomEvent(`month-changed-${monthPickerId}`, {
                detail: {
                    value: input.value,
                    id: monthPickerId
                }
            });
            document.dispatchEvent(event);
        }

        setActiveButton();
    });
</script>
@endpush

