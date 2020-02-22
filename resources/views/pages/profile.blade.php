@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row justify-content-center">
            @foreach($movies as $movie)
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-body border text-center">
                            {{$movie->title}}
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
@endsection
