<?php

namespace App\Service;

use Illuminate\Support\Facades\Auth;

class ContestService {


    // 좋아요 클릭 함수
    public function like($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $id = Auth::user()->id;

            \App\Models\ContestLikeHistory::clickTolike($id, $request['contest_id']);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Contest Like Service '.$e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }


    // 좋아요 리스트 함수
    public function likeList($request) {

        $success = false;
        $msg     = '';
        $data    = [];

        try {

            $id = Auth::user()->id;

            $data['list'] = \App\Models\ContestLikeHistory::list($id);
            $success = true;

        } catch (\Exception $e) {
            $msg = 'Error : Contest Like List Service '.$e;
        }

        return response()->json([ 'success' => $success, 'msg' => $msg, 'data' => $data ]);

    }

}
