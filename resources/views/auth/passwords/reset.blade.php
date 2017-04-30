@extends('layouts.app')
@section('title', 'Reset Password')
@section('content')

    <div class="columns">
        <div class="column is-9-mobile is-offset-1-mobile is-6-tablet is-offset-3-tablet is-4-desktop is-offset-4-desktop">

            <nav class="panel">
                <p class="panel-heading">Reset Password</p>
                <div class="panel-block">

                    <form class="control" action="{{ url('/password/reset') }}" method="POST">
                        {{ csrf_field() }}
                            
                        <input type="hidden" name="token" value="{{ $token }}">

                        <div class="field">
                            <label class="label">Email</label>
                            <p class="control">
                                <input class="input" type="text" name="email" placeholder="Email" 
                                    value="{{ $email or old('email') }}">
                                <span class="help is-danger">{{ $errors->first('email') }}</span>
                            </p>
                        </div>

                        <div class="field">
                            <label class="label">Password</label>
                            <p class="control">
                                <input class="input" type="password" name="password" placeholder="Password">
                                <span class="help is-danger">{{ $errors->first('password') }}</span>
                            </p>
                        </div>

                        <div class="field">
                            <label class="label">Confirm Password</label>
                            <p class="control">
                                <input class="input" type="password" name="password_confirmation" 
                                    placeholder="Confirm password">
                                <span class="help is-danger">{{ $errors->first('password_confirmation') }}</span>
                            </p>
                        </div>

                        <div class="field">
                            <p class="control">
                                <button class="button is-success">Reset Password</button>
                            </p>
                        </div>
                    </form>
                </div>
            </nav>
        </div>
    </div>
@endsection