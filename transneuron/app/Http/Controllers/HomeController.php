<?php

namespace App\Http\Controllers;

use App\friendList;
use App\User;
use Illuminate\Support\Facades\DB;
use Pusher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

include public_path() . "/../vendor/autoload.php";

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     * author Sitesh Ranjan date 12th may 2018
     * getting all the users details
     * getting all the accepted user requested
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $userId['id'] = Auth::id();

        //calling model for get the friend details
        $objModelFriendList = new friendList();
        //calling model for get the all users
        $objModelAllAppUser = new User();

        $resultFriendList = $objModelFriendList->getAllFriendList($userId);
        $resultAllPendingFriendList = $objModelFriendList->getAllPendingFriendList($userId);
        $allUsers = $objModelAllAppUser->getallAppUser(Auth::id(), $resultFriendList);
        return view('home', ['resultFriendList' => $resultFriendList, 'allUsers' => $allUsers, 'userId' => Auth::id(), 'resultAllPendingFriendList' => $resultAllPendingFriendList]);
    }

    /**
     * @param Request $request
     */
    public function searchByKey(Request $request)
    {
        $search_Key = $request->input('search_Key');
        $objModelAllAppUser = new User();
        $resultFriendList = $objModelAllAppUser->getAllsearchdata($search_Key);
        echo $resultFriendList;

    }

    /*
     * @author Sitesh Ranjan
     * @date 13-may-2018
     * @getting mutual friends details
     */
    public function userProfile(Request $request, $id)
    {
        $authID = Auth::id();
        $frndIDArr = [];
        $opfrndIDArr = [];
        $myFried = DB::SELECT('SELECT * FROM `friend_list` WHERE from_user_id = ' . $authID . ' OR to_user_id = ' . $authID . ' ORDER BY `status`  DESC');
        $oppsiteFriendList = DB::SELECT('SELECT * FROM `friend_list` WHERE from_user_id = ' . $id . ' OR to_user_id = ' . $id . ' ORDER BY `status`  DESC');
        if (sizeof($myFried) > 0) {
            foreach ($myFried as $key => $val) {
                if ($val->to_user_id == $authID) {
                    $frndIDArr[] = $val->from_user_id;
                } else {
                    $frndIDArr[] = $val->to_user_id;
                }
            }
        }

        if (sizeof($oppsiteFriendList) > 0) {
            foreach ($oppsiteFriendList as $key => $val) {
                if ($val->to_user_id == intval($id)) {
                    $opfrndIDArr[] = $val->from_user_id;
                } else {
                    $opfrndIDArr[] = $val->to_user_id;
                }
            }
        }
        $mutualFrndID = array_intersect($frndIDArr, $opfrndIDArr);
        if (sizeof($mutualFrndID) == 0) {
            $mutualFrndID[] = 0;
        }
        $mutualFrndDetails = DB::SELECT('SELECT * FROM `users` WHERE id IN (' . implode(",", $mutualFrndID) . ')');
        return view('user_details', ['result' => $mutualFrndDetails]);
    }

    /**
     * @param Request $request
     */
    public function acceptFriend(Request $request)
    {
        $to_user_id = Auth::id();
        $status['status'] = "A";

        $friendReqID = $request->input('frnd_id');
        $options = array(
            'cluster' => 'ap2',
            'encrypted' => true
        );
        $pusher = new Pusher\Pusher(
            'af1477bc8d09383299b9',
            'ce808a61da64a830a6cd',
            '524619',
            $options
        );
        $data = [
            'message' => Auth::user()->name . ' have accepted your request',
            'friendRequestUserID' => Auth::id()
        ];
        try {
            $status['status'] = "A";
            $status['message'] = $data['message'];
            $result = DB::table('friend_list')
                ->where('from_user_id', $friendReqID)
                ->where('to_user_id', $to_user_id)
                ->update($status);
            $pusher->trigger('friendAcceptresponse', $friendReqID, $data);

            if ($result) {
                echo 1;
            } else {
                echo 0;
            }
        } catch (\Throwable $e) {
        }
    }

    /**
     * @param Request $request
     */
    public function declinedRequest(Request $request)
    {

        $to_user_id = Auth::id();
        $status['status'] = "D";

        $friendReqID = $request->input('frnd_id');
        $options = array(
            'cluster' => 'ap2',
            'encrypted' => true
        );
        $pusher = new Pusher\Pusher(
            'af1477bc8d09383299b9',
            'ce808a61da64a830a6cd',
            '524619',
            $options
        );
        $data = [
            'message' => Auth::user()->name . ' have declined your request',
            'friendRequestUserID' => Auth::id()
        ];
        try {
            $status['status'] = "D";
            $result = DB::table('friend_list')
                ->where('from_user_id', $friendReqID)
                ->where('to_user_id', $to_user_id)
                ->update($status);
            $pusher->trigger('declineresponse', $friendReqID, $data);

            if ($result) {
                echo 1;
            } else {
                echo 0;
            }

        } catch (\Throwable $e) {

        }
    }

    /**
     * @param Request $request
     */
    public function addFriendToTheList(Request $request)
    {
        if ($request->isMethod('post')) {
            $friendReqID = $request->input('userID');
            $options = array(
                'cluster' => 'ap2',
                'encrypted' => true
            );
            $pusher = new Pusher\Pusher(
                'af1477bc8d09383299b9',
                'ce808a61da64a830a6cd',
                '524619',
                $options
            );
            $data = [
                'message' => 'Hi I am ' . Auth::user()->name . ' will you be my friend',
                'friendRequestUserID' => Auth::id()
            ];
            try {

                //saving data in database
                $userData['from_user_id'] = $data['friendRequestUserID'];
                $userData['to_user_id'] = $friendReqID;
                $userData['message'] = $data['message'];
                $userData['status'] = 'P';

                $checkExist = DB::table('friend_list')
                    ->where('from_user_id', $data['friendRequestUserID'])
                    ->where('to_user_id', $friendReqID)
                    ->get();

                if (sizeof($checkExist) == 0) {
                    $pusher->trigger('friendReq', $friendReqID, $data);
                    $result = DB::table('friend_list')
                        ->insert($userData);
                    if ($result) {
                        echo 1;
                    } else {
                        echo 0;
                    }
                } else {
                    if ($checkExist[0]->status == 'D') {
                        $status['status'] = 'P';
                        $pusher->trigger('friendReq', $friendReqID, $data);
                        $result = DB::table('friend_list')
                            ->where('from_user_id', $data['friendRequestUserID'])
                            ->where('to_user_id', $friendReqID)
                            ->update($status);
                        if ($result) {
                            echo 3;
                        }
                    } else {
                        echo 2;
                    }
                }
            } catch (\Throwable $e) {
            }
        }
    }
}
