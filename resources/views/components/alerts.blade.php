<div style="width: 50%;margin:auto;">
    @if (session('success'))
    <br>
    <div class="alert alert-success" role="alert">
        {{ session('success') }}
    </div>
    @endif

    @if (session('error'))
    <br>
    <div class="alert alert-danger" role="alert">
        {{session('error')}}
    </div>
    @endif
</div>