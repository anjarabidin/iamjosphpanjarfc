<label for="dateRange" class="btn btn-sm btn-light text-dark fw-600 d-flex align-items-center px-4">
    <input placeholder="Pick date rage" class="bg-transparent text-dark fw-600 cursor-pointer" id="dateRange" />
    <i class="ki-duotone ki-calendar fs-1 ms-0 me-0">
        <span class="path1"></span>
        <span class="path2"></span>
        <span class="path3"></span>
        <span class="path4"></span>
        <span class="path5"></span>
        <span class="path6"></span>
    </i>
</label>
@push('js')
<script type="text/javascript" src="https://cdn.jsdelivr.net/momentjs/latest/moment.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.1/locale/id.min.js"></script>
<script type="text/javascript" src="https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js"></script>
<script>
    $(function() {
        var start = moment().startOf('month');
        var end = moment().endOf('month');

        function cb(start, end) {
            $('#dateRange span').html(start.format('D/MM/YYYY') + ' - ' + end.format('D/MM/YYYY'));
            var start = start.format('YYYY-MM-DD');
            var end = end.format('YYYY-MM-DD');
            $('#start_date').val(start);
            $('#end_date').val(end);
            table();
        }

        $('#dateRange').daterangepicker({
            startDate: start,
            endDate: end,
            ranges: {
                'Semua Waktu': [moment().subtract(5, 'years'), moment()],
                'Hari Ini': [moment(), moment()],
                'Kemarin': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
                'Bulan Ini': [moment().startOf('month'), moment().endOf('month')],
                'Bulan Kemarin': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')],
                '7 Hari Terakhir': [moment().subtract(6, 'days'), moment()],
                '30 Hari Terakhir': [moment().subtract(29, 'days'), moment()],
                'Tahun Ini': [moment().startOf('year'), moment().endOf('year')],
                'Tahun Kemarin': [moment().subtract(1, 'year').startOf('year'), moment().subtract(1, 'year').endOf('year')],
            }
        }, cb);
        cb(start, end);
    });
</script>
@endpush
