<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

/**
 * Class friendList
 * @package App
 */
class friendList extends Model
{
    /**
     * @var array
     */
    protected $fillable = [
        'to_user_id', 'from_user_id', 'status',
    ];

    /**
     * @var string
     */
    protected $table = 'friend_list';

    /**
     * @return string
     */
    public function getAllFriendList()
    {
        if (func_num_args() > 0) {
            $userId = func_get_arg(0);
            try {
                $authID = Auth::id();
                $frndID = [];
            //    $frnlist = DB::SELECT('SELECT * FROM `friend_list` WHERE from_user_id = ' . $authID . ' OR to_user_id = ' . $authID . ' ORDER BY `status`  DESC');
                $frnlist=DB::table('friend_list')
                    ->where('from_user_id',$authID)
                    ->orWhere('to_user_id',$authID)
                    ->where('status','A')
                    ->orderBy('status','DESC')
                    ->get();
//               print_r($frnlist);die;
                if (sizeof($frnlist) > 0) {
                    foreach ($frnlist as $key => $val) {
                        if ($val->to_user_id == $authID) {
                            $frndID[] = $val->from_user_id;
                        } else {
                            $frndID[] = $val->to_user_id;
                        }
                    }
                } else {
                    $frndID[] = 0;
                }
                $frendDetails = DB::SELECT('SELECT id,name,email FROM users WHERE id IN (' . implode(",", $frndID) . ')');
                return $frendDetails;
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            throw new Exception('Argument Not Passed');
        }
    }

    /**
     * @param $userId
     * @return string
     */
    public function getAllPendingFriendList($userId)
    {
        if (func_num_args() > 0) {
            $userId = func_get_arg(0);
            try {
                $authID = Auth::id();
                $result = DB::table($this->table)
                    ->join('users', 'friend_list.from_user_id', '=', 'users.id')
                    ->where('to_user_id', $authID)
                    ->where('status', 'P')
                    ->select('friend_list.to_user_id', 'friend_list.message', 'friend_list.from_user_id', 'users.name', 'users.email', 'friend_list.status')
                    ->get();
                return $result;
            } catch (\Exception $e) {
                return $e->getMessage();
            }
        } else {
            throw new Exception('Argument Not Passed');
        }
    }
}
