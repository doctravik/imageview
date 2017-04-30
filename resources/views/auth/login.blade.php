@extends('layouts.app')
@section('title', 'Login')
@section('content')
    <div class="columns">
        <div class="column is-9-mobile is-offset-1-mobile is-6-tablet is-offset-3-tablet is-4-desktop is-offset-4-desktop">

            <nav class="panel">
                <p class="panel-heading">Login</p>
                <div class="panel-block">

                    <form class="control" action="{{ url('/login') }}" method="POST">
                        {{ csrf_field() }}

                        <div class="field">
                            <label class="label">Email</label>
                            <p class="control is-expanded">
                                <input class="input" type="text" name="email" placeholder="Email" 
                                    value="{{ old('email') }}">
                                <span class="help is-danger">{{ $errors->first('email') }}</span>
                            </p>
                        </div>

                        <div class="field">
                            <label class="label">Password</label>
                            <p class="control is-expanded">
                                <input class="input" type="password" name="password" placeholder="Password">
                                <span class="help is-danger">{{ $errors->first('password') }}</span>
                            </p>
                        </div>

                        <nav class="level is-marginless">
                            <div class="level-left">
                                <div class="level-item">
                                    <button class="button is-success">Login</button>
                                </div>
                            </div>
                            <div class="level-right">
                                <p class="level-item">
                                    <a href="{{ url('/password/reset') }}">Remind password</a>
                                </p>
                            </div>
                        </nav>
                    </form>
                </div>
            </nav>
        </div>
    </div>
@endsection