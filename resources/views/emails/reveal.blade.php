@extends('layouts.app')

@section('content')
<div class="container" id="revealEmailApp">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <email-reveal-form email="{{$email->uuid}}"/>
        </div>
    </div>
</div>
@endsection
