
@if(!empty(session('status')))
<div class="alert alert-status">
    {{ session('status.message') }}
</div>
@endif

@if(!empty(session('error')))
    <div class="alert alert-danger">
        {{ session('error.message') }}
    </div>
@endif

@if(!empty(session('success')))
    <div class="alert alert-success">
        {{ session('success.message') }}
    </div>
@endif

@if(!empty(session('warning')))
    <div class="alert alert-warning">
        {{ session('warning.message') }}
    </div>
@endif
