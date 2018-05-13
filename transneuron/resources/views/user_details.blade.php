@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row">
            <div class="col-md-8 col-md-offset-2">
                <div class="panel panel-default">
                    <div class="panel-heading">Dashboard</div>

                    <div class="panel-body">
                        @if (session('status'))
                            <div class="alert alert-success">
                                {{ session('status') }}
                            </div>
                        @endif

                        welcome to the dash board
                    </div>


                </div>
            </div>

        </div>
        <div>
            <div class="col-md-12">
                <h2> My Mutual Friend </h2>

                <div class="myFrndReq">
                    @foreach($result as $data)
                        <li style="list-style-position:inside;border: 1px solid black;">
                            {{$data->name}}
                            {{$data->email}}
                            {{--<button class="btn btn-primary accept_request" id="{{$data->from_user_id}}">Accept</button>--}}
                        </li>
                    @endforeach
                </div>


            </div>
        </div>
    </div>
@endsection
