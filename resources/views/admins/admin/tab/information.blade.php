<h3 class="card-title mb-4">
    <span class="card-label fw-bold fs-3">Informasi Admin</span>
</h3>
<div class="row">
    <div class="col-sm-2">
        <img src="{{asset($admin->avatar)}}" class="img mb-2 img-thumbnail" alt="">
    </div>
    <div class="col">
        <div class="row">
            <div class="col-sm-3">
                <label>Nama</label>
                <p class="text-label">{{$admin->name}}</p>
            </div>
            <div class="col-sm-3">
                <label>Email</label>
                <p class="text-label">{{$admin->email}}</p>
            </div>
            <div class="col-sm-3">
                <label>Role</label> <br>
                <p class="text-label">{{$admin->role_name}}</p>
            </div>
            <div class="col-sm-3">
                <label>Hak Akses</label> <br>
                @foreach ($admin->roles()->first()->permissions as $permission )
                <span class="badge badge-info m-1">{{$permission->name}}</span>
                @endforeach
            </div>
        </div>
    </div>
</div>
