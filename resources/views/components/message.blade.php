 @if(Session::has('success'))
    <div class="alert alert-success p-3 mb-3 rounded">
        {{ Session::get('success') }}
    </div>
@endif

@if(Session::has('error'))
    <div class="alert alert-danger p-3 mb-3 rounded">
        {{ Session::get('error') }}
    </div>
@endif
