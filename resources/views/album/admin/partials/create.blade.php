<form action="{{ url('/admin/albums') }}" method="POST" class="control notification box">
    {{ csrf_field() }}
    <div class="field">
        <label class="label">Name</label>
        <p class="control">
            <input class="input" name="name" type="text" placeholder="Name" value="{{ old('name') }}">
        </p>
        @if($errors->has('name'))
            <p class="help is-danger">{{ $errors->first('name') }}</p>
        @endif
    </div>
    <div class="field">
        <p class="control">
            <button class="button is-primary">Create album</button>
        </p>
    </div>
</form>