@extends('layouts.app')
@section('title', 'Confirm your email')
@section('content')
    <div class="columns">
        <div class="column is-6 is-offset-3">
            <nav class="panel">
                <p class="panel-heading">Confirm email</p>
                <div class="panel-block is-fullwidth has-text-centered">
                    <div class="tile is-vertical is-parent">
                        <p class="subtitle">To get access to your dashboard you should confirm your email.</p>
                        <div class="tile is-child">
                            <form action="{{ route('activation.token.resend', $user) }}" method="POST">
                                {{ csrf_field() }}
                                <button class="button is-success">Resend token to my email.</button>
                            </form>
                        </div>
                    </div>
                </div>
            </nav>
        </div>
    </div>
@endsection