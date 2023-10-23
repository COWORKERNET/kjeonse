<?php
namespace App\Http\Controllers\Views;

use App\Http\Controllers\Controller;

use App\Service\ViewService;
use Illuminate\Http\Request;


class ViewController extends Controller
{
    // Service Layer
    protected $viewService;

    public function __construct(ViewService $viewService) { $this->viewService = $viewService; }

    /* ------------------------------------------------------------------------------------------------------------- */
    // 사용자 화면 내 데이터 출력용 함수
    public function main() { return $this->viewService->main(); }
    /* ------------------------------------------------------------------------------------------------------------- */


    /* ------------------------------------------------------------------------------------------------------------- */
    // 게시판 조회 [ 0: guide, 1: notice, 2: faq ]
    public function guide()                     { return $this->viewService->boardList(0); }
    public function notice()                    { return $this->viewService->boardList(1); }
    public function faq(Request $request)       { return $this->viewService->boardList(2, $request->all()); }
    public function bank()                      { return $this->viewService->bank(); }
    /* ------------------------------------------------------------------------------------------------------------- */



    /* ------------------------------------------------------------------------------------------------------------- */
    // 게시판 상세 조회 [ 0: guide, 1: notice, 2: faq ]
    public function detail_guide    (Request $request)  { return $this->viewService->boardDetail(0, $request->all()); }
    public function detail_notice   (Request $request)  { return $this->viewService->boardDetail(1, $request->all()); }
    /* ------------------------------------------------------------------------------------------------------------- */



    /* ------------------------------------------------------------------------------------------------------------- */
    // Faq Sub Tab Category List 자주 묻는 질문 카테고리 변경을 고려하여 별도 테이블로 구성하였으며,
    // 프론트단에서 카테고리 데이터를 전달 받아 출력 시켜주고 있다.
    public function category_board_faq() { return $this->viewService->getBoardFaqCategory(); }
    /* ------------------------------------------------------------------------------------------------------------- */



    /* ------------------------------------------------------------------------------------------------------------- */
    // Board Search
    public function search_guide    (Request $request) { return $this->viewService->boardSearch(0, $request->all()); }
    public function search_notice   (Request $request) { return $this->viewService->boardSearch(1, $request->all()); }
    public function search_faq      (Request $request) { return $this->viewService->boardSearch(2, $request->all()); }
    /* ------------------------------------------------------------------------------------------------------------- */



    /* ------------------------------------------------------------------------------------------------------------- */
    // Contest
    public function contest(Request $request)        { return $this->viewService->contestList($request->all()); }
    public function contest_detail(Request $request) { return $this->viewService->contestDetail($request->all()); }
    /* ------------------------------------------------------------------------------------------------------------- */
}
