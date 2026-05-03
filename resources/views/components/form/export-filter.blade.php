
<form action="{{ $url }}" method="post">
    @csrf
<div class="row ">
    <div class="col-md-4 form-group">
        <label for="date">From Date</label>
        <div>
            <input type="text" class="form-control datepicker input-sm" name="fromDate" value="" required />
        </div>
    </div>
    <div class="col-md-4 form-group">
        <label for="date">To Date</label>
        <div>
            <input type="text" class="form-control datepicker input-sm" name="toDate" value="" required />
        </div>
    </div>
    <div class="col-auto">
        <button type="submit" class="btn btn-outline-success" style="margin-top: 40px;"><i class="fas fa-file-excel"></i> Export</button>
    </div>
</div>
</form>
