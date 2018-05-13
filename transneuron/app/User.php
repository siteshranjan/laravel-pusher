<?php

namespace App;

use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;
use DB;

/**
 * Class User
 * @package App
 *
 */
class User extends Authenticatable
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password',
    ];

    protected $table = 'users';

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * @param $userId
     * @param $resultFriendList
     * @return string
     */
    public function getallAppUser($userId, $resultFriendList)
    {
        try {
            $id = array();
            if (sizeof($resultFriendList) > 0) {
                foreach ($resultFriendList as $data) {
                    $id[] = $data->id;
                }
            } else {
                $id[] = 0;
            }
            if (sizeof($id) == 0) {
                $sqlQuery = "SELECT * FROM users WHERE id != '" . $userId . "'";
            } else {
                $sqlQuery = "SELECT * FROM users WHERE id != '" . $userId . "' AND id NOT IN (" . implode(",", $id) . ")";
            }
            $result = DB::select($sqlQuery);
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * @param $search_Key
     * @return string
     */
    public function getAllsearchdata($search_Key)
    {
        try {
            $result = DB::table($this->table)
                ->Where('name', 'like', '%' . $search_Key . '%')
                ->orWhere('email', 'like', '%' . $search_Key . '%')
                ->select('id', 'name', 'email')
                ->get();
            return $result;
        } catch (\Exception $e) {
            return $e->getMessage();
        }

    }
}
