<?php

namespace App\Service;

use App\Models\Banner;
use App\Models\Guide;
use App\Models\Faq;
use App\Models\Notice;
use App\Models\Associate;
use App\Models\About;
use App\Models\CategoryBoard;
use App\Models\BoardFiles;
use App\Models\Contest;
use App\Models\ContestInfo;
use App\Models\ContestFiles;
use App\Models\Popup;
use Illuminate\Support\Facades\Auth;


class ViewService {

    public function __construct() { }

    /* -- */
    // User Model Banner, Associate, Notice, Guide, FAQ
    public function main() {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $data['banners']    = Banner::main();
            $data['about']      = About::show();
            $data['associate']  = Associate::main();
            $data['notice']     = Notice::main();
            $data['guide']      = Guide::main();
            $data['faq']        = Faq::main();
            $data['popup']      = Popup::list();

            $success = true;

        } catch (\Exception $e) {
            $msg = $e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);
    }
    /* -- */


    /* -- */
    // User Model Banner, Associate, Notice, Guide, FAQ
    public function bank() { return \App\Models\Bank::list(); }
    /* -- */


    /* -- */
    // User Board List : $type 별 Switch-case 처리
    public function boardList($type, $request = null) {

        $success = false;
        $msg = '';
        $data = [];

        // 0 guide, 1 notice, 2 faq
        try {

            switch ($type) {

                case 0:
                    $data['list'] = Guide::list();
                    break;

                case 1:
                    $data['list'] = Notice::list();
                    break;

                case 2:
                    !empty($request) ?
                        $data['list'] = Faq::list($request['type'])
                        :
                        $data['list'] = Faq::list(null);

                    break;

                default:
                    break;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = $e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);
    }
    /* -- */



    /* -- */
    // User Board Search
    public function boardSearch($type ,$request = null) {

        $success = false;
        $msg = '';
        $data = [];

        // 0 guide, 1 notice, 2 faq
        try {

            switch ($type) {

                case 0:
                    $data['list'] = Guide::search($request['search']);
                    break;

                case 1:
                    $data['list'] = Notice::search($request['search']);
                    break;

                case 2:
                    !empty($request['type']) ?
                        $data['list'] = Faq::search($request['type'], $request['search'])
                        :
                        $data['list'] = Faq::search(null, $request['search']);

                    break;

                default:
                    break;
            }

            $success = true;

        } catch (\Exception $e) {
            $msg = $e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }
    /* -- */



    /* -- */
    // User Faq Type
    public function getBoardFaqCategory() { return CategoryBoard::list(); }
    /* -- */



    /* -- */
    // User Board Detail
    public function boardDetail($type, $request) {

        $success = false;
        $msg = '';
        $data = [];

        // 0 guide, 1 notice, 2 faq
        try {

            $model = null;

            switch ($type) {

                case 0:
                    $model = new Guide();
                    break;

                case 1:
                    $model = new Notice();
                    break;

                default:
                    break;
            }
            $id = $request['id'];

            $data['detail']     = $model::detail($id);
            $data['previous']   = $model::getPreviousPage($id);
            $data['next']       = $model::getNextPage($id);
            $data['files']      = BoardFiles::getFiles($type, $id);

            $success = true;

        } catch (\Exception $e) {
            $msg = $e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }
    /* -- */



    /* -- */
    // User Contest List
    public function contestList($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $type = $request['type'] ?? "";
            $keyword = $request['keyword'] ?? "";

            $data['contest'] = Contest::list($type, $keyword);
            $success = true;

        } catch (\Exception $e) {
            $msg = $e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);
    }



    /* -- */
    // User Contest Detail
    public function contestDetail($request) {

        $success = false;
        $msg = '';
        $data = [];

        try {

            $id = $request['id'];

            $user_id = null;
            if(!empty(Auth::user()->id)) {
                $user_id = Auth::user()->id;
            }

            $data['detail'] = Contest::detail($id, $user_id);
            $data['info']   = ContestInfo::detail($id);
            $data['files']  = ContestFiles::getFiles($id);

            $success = true;

        } catch (\Exception $e) {
            $msg = $e;
        }

        return response()->json(['success' => $success, 'msg' => $msg, 'data' => $data]);

    }

}
