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

        <div align="right">
            <form action="">
                <input type="text" id="key_search">
                <button id="search">search</button>
            </form>
        </div>
        <div align="right" class="search-section">

        </div>
        <div>
            <div class="col-md-12">
                <h2> My Friend</h2>
                @if(count($resultFriendList) > 0)
                    @foreach($resultFriendList as $data)
                        <div class="col-lg-3" style="border:solid">
                            <img src="">
                            <h4>{{$data->name}}</h4>
                            <p>{{$data->email}}</p>
                            <a href="/user-profile/{{$data->id}}" class="btn btn-primary"> view profile </a>
                        </div>
                    @endforeach
                @else
                    No Friend are available in your friend list
                @endif
            </div>
        </div>
        <div>
            <div class="col-md-12">
                <h2> Notifications</h2>

                <div class="myFrndReq">
                    @if(count($resultAllPendingFriendList) > 0)
                        @foreach($resultAllPendingFriendList as $data)
                            <li style="list-style-position:inside;border: 1px solid black;">
                                Hi I am {{$data->name}} will you be my friend
                                <button class="btn btn-primary accept_request" id="{{$data->from_user_id}}">Accept
                                </button>
                                <button class="btn btn-danger accept_declined" id="{{$data->from_user_id}}">Decline
                                </button>
                            </li>
                        @endforeach
                    @else
                        <span style="color: red">Friend request will appear here </span>
                    @endif
                </div>


            </div>
        </div>

        <div>
            <div class="col-md-12">
                <h2>App Users</h2>
                @if(count($allUsers) > 0)
                    @foreach($allUsers as $users)
                        <div class="col-lg-3" style="border:solid">
                            <h4>{{$users->name}}</h4>
                            <input type="hidden" name="userName" value="{{$users->name}}">
                            <p>{{$users->email}}</p>
                            <input type="hidden" name="userEmail" value="{{$users->email}}">
                            <input type="hidden" id="userID" value="{{$users->id}}">
                            <button class="btn btn-primary add_friend" id="{{$users->id}}">Add Friend</button>
                        </div>
                    @endforeach
                @else
                    No Users Available
                @endif
            </div>
        </div>


    </div>
@endsection

<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.7.1/jquery.min.js" type="text/javascript"></script>
<script src="https://js.pusher.com/4.1/pusher.min.js"></script>

<script>

    var id = '<?php echo $userId; ?>';
    // Enable pusher logging - don't include this in production
    Pusher.logToConsole = true;

    var pusher = new Pusher('af1477bc8d09383299b9', {
        cluster: 'ap2',
        encrypted: true
    });

    var channel = pusher.subscribe('friendReq');
    channel.bind(id, function (data) {

        var html = '<li style="list-style-position:inside;border: 1px solid black;">';
        html += data.message;
        html += '<button class="btn btn-primary accept_request" id="' + data.friendRequestUserID + '">Accept</button>';
        html += '<button class="btn btn-danger accept_declined" id="' + data.friendRequestUserID + '">Decline</button>';
        html += "</li>";
        $('.myFrndReq').append(html);

    });

    var channel1 = pusher.subscribe('friendAcceptresponse');
    channel1.bind(id, function (data) {

        var html = '<li style="list-style-position:inside;border: 1px solid black;">';
        html += data.message;
        html += "</li>";
        $('.myFrndReq').append(html);
        location.reload();

    });


    var channel12 = pusher.subscribe('declineresponse');
    channel12.bind(id, function (data) {

        var html = '<li style="list-style-position:inside;border: 1px solid black;">';
        html += data.message;
        html += "</li>";
        $('.myFrndReq').append(html);
        //location.reload();

    });


</script>

<script>
    $(document).ready(function () {
        $('#search').click(function (e) {
            e.preventDefault();
            var search_Key = $('#key_search').val();
            $.ajax({
                url: '/searchByKey',
                type: 'post',
                dataType: 'json',
                data: {
                    search_Key: search_Key
                },
                beforeSend: function () {
                },
                success: function (response) {
                    $(".search-section").empty();
                    if (response != '') {
                        $.each(response, function (index, value) {
                            $(".search-section").append(' <div class="col-lg-3" style="border:solid" align="right"><h4>' + value['name'] + '</h4> <p>' + value['email'] + '</p></div>');
                        });
                    } else {
                        $(".search-section").append('<div class="row"><strong> No details found </strong></div>');

                    }
                }
            });
        })

        $(document).on('click', '.accept_request', function (e) {
            e.preventDefault();
            var frnd_id = $(this).attr('id');

            $.ajax({
                url: '/acceptFriend',
                type: 'post',
                dataType: 'json',
                data: {
                    frnd_id: frnd_id
                },
                beforeSend: function () {
                },
                success: function (response) {
                    if (response == 1) {
                        alert('friend accepted sucessfully');
                        location.reload();
                    } else {
                        alert('something went wrong please try again');
                    }
                }
            });
        });

        $(document).on('click', '.accept_declined', function (e) {
            e.preventDefault();
            var frnd_id = $(this).attr('id');
            $.ajax({
                url: '/declinedRequest',
                type: 'post',
                dataType: 'json',
                data: {
                    frnd_id: frnd_id
                },
                beforeSend: function () {
                },
                success: function (response) {
                    if (response == 1) {
                        alert('User declined successfully');
                        location.reload();
                    } else {
                        alert('something went wrong please try again');
                    }
                }
            });
        });
    });
</script>

<script>

    $(document).on('click', '.add_friend', function (e) {
        var frnd_id = $(this).attr('id');
        $.ajax({
            url: '/addFriendToTheList',
            type: 'post',
            dataType: 'json',
            data: {
                userID: frnd_id
            },
            beforeSend: function () {
            },
            success: function (response) {
                if (response == 1) {
                    alert('friend request send successfully');
                } else if (response == 2) {
                    alert('friend already send');
                } else if (response == 3) {
                    alert('request again send successfully');
                } else {
                    alert('something went wrong');
                }
            }
        });
    });
</script>