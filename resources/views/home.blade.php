@extends('layouts.app')

@section('content')
<div class="container" id="app">
    <div class="row justify-content-center">
        <div class="col-md-3">
            <sidebar></sidebar>
        </div>
        <div class="col">
            <router-view></router-view>
        </div>
    </div>
</div>
@endsection
