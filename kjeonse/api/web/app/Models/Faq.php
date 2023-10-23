<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'board_faq';

    // 메인화면 FAQ 리스트
    protected function main() {
        return $this->join('category_board', 'category_board.id', 'board_faq.ref_category_board_id')
            ->select(
                'board_faq.id',
                'board_faq.title',
                'category_board.title as category',
                'board_faq.content',
                'board_faq.created_at',
            )
            ->where('board_faq.status', '=', 0)
            ->orderByDesc('board_faq.created_at')
            ->get(6);
    }

    // User Board List
    protected function list($type) {

        $res = $this->join('category_board', 'category_board.id', 'board_faq.ref_category_board_id')
            ->select(
                'board_faq.id',
                'board_faq.title',
                'category_board.id as category_id',
                'category_board.title as category',
                'board_faq.content',
                'board_faq.created_at',
            )
            ->where('board_faq.status', '=', 0)
            ->orderByDesc('board_faq.id');

        if (!empty($type)) {
            return $this->scopeBoardFaq($res, $type)->paginate(10);;
        }

        return $res->paginate(10);

    }

    // User Board List
    protected function search($type, $search) {

        $res = $this->join('category_board', 'category_board.id', 'board_faq.ref_category_board_id')
            ->select(
                'board_faq.id',
                'board_faq.title',
                'category_board.id as category_id',
                'category_board.title as category',
                'board_faq.content',
                'board_faq.created_at',
            )
            ->where('board_faq.status', '=', 0)
            ->where('board_faq.title', 'like', '%'.$search.'%')
            ->orderByDesc('board_faq.created_at');

        if (!empty($type)) {
            return $this->scopeBoardFaq($res, $type)->paginate(10);;
        }

        return $res->paginate(10);

    }

    // User Board Detail
    protected function detail($idx) { return $this->where('status', 0)->where('id', $idx)->first(); }

    // Search Scope
    private function scopeBoardFaq($query, $type) { return $query->where('category_board.id', $type); }


    /* ----------------------------------------------------------- */
    //
    protected function admin_list() {
        return $this->join(
            'category_board', 'category_board.id', '=', 'board_faq.ref_category_board_id'
        )->select(
            'board_faq.id',
            'category_board.title as category',
            'board_faq.title',
            'board_faq.content',
            'board_faq.created_at',
        )->where('board_faq.status', 0)->orderByDesc('board_faq.id')->get();
    }

    //
    protected function admin_detail($id) {
        return $this->leftJoin(
            'category_board', 'category_board.id', '=', 'board_faq.ref_category_board_id'
        )
            ->select(
            'board_faq.id',
            'category_board.title as category',
            'board_faq.title',
            'board_faq.content',
        )
            ->where('board_faq.status', 0)
            ->where('board_faq.id', $id)
            ->first();
    }

    //
    protected function deleteContent($id) {
        return $this->where('id', $id)->update([
            'status' => 1,
        ]);
    }

    //
    protected function updateContent($data, $admin_id) {
        return $this->where('id', $data['id'])
            ->update([
                'ref_admin_id' => $admin_id,
                'ref_category_board_id' => $data['category_id'],
                'title' => $data['title'],
                'content' => $data['content'],
            ]);
    }

    //
    protected function create($data) {
        return $this->insertGetId([
            'ref_category_board_id' => $data['category_id'],
            'title' => $data['title'],
            'content' => $data['content'],
        ]);
    }

    /* ----------------------------------------------------------------------------------------------------- */
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
