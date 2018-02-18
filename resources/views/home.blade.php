@extends('layouts.app')


@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Dashboard
                    </div>
                    <div class="panel-body">
                        <p>Welcome {{ Auth::user()->name }} to Share Space home page!</p>
                        <p>Your upload key is <b><u>{{ Auth::user()->key }}</u></b> &nbsp;put it as an argument on the custom destination. </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('user thumbnails')
    @foreach( $Files->chunk(6) as $row)
        <div class="row">
            @foreach( $row as $File )
                <div class="col-md-2">
                    <div class="panel panel-default">
                        <img class="center-block" src="{{ $File->thumbpath }}">
                        <button type="button" class="btn btn-block" onclick="window.location='{{ $File->fullurl }}'">View Full size</button>
                        <button type="button" class="btn btn-block" data-clipboard-text='{{ $File->fullurl }}'>Copy Link</button>
                        <button type="button" class="btn btn-block" onclick="window.location='{{ action('FileController@deleteImage', ['deletionkey' => $File->deletionkey]) }}' ">Delete Image</button><br>
                    </div>
                </div>
            @endforeach
        </div>
    @endforeach
@endsection
