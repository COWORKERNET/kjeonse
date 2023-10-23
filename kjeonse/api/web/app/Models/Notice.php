<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Notice extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'board_notice';

    public function __construct() { return $this; }

    // User Main
    protected function main() {
        return $this->select(
            'id',
            'title',
            'content',
            'created_at',
        )
        ->orderByDesc('created_at')
        ->where('status', '=', 0)
        ->get(10);
    }

    // User Board List
    protected function list() {
        return $this->select(
                    'id',
                    'title',
                    'top',
                    'content',
                    'created_at',
                )
                ->orderByDesc('top')
                ->orderByDesc('created_at')
                ->where('status', '=', 0)
                ->paginate(10);
    }

    // User Board Search
    protected function search($search) {
        return $this->select(
            'id',
            'title',
            'top',
            'content',
            'created_at',
        )
        ->where('status', '=', 0)
        ->where('title', 'like', '%'.$search.'%')
        ->orderByDesc('top')
        ->orderByDesc('id')
        ->paginate(10);
    }

    //
    protected function detail($idx){ return $this->where('status', 0)->where('id', $idx)->first(); }

    //
    protected function getPreviousPage($idx) {

        $res = $this->select('id')->orderByDesc('top')->orderByDesc('id')->get();

        if (!empty($res)) {
            foreach ($res as $index => $item) {
                if($item->id == $idx) { return empty($res[$index+1]) ? null : $res[$index+1]; }
            }
        }

        return null;
    }

    //
    protected function getNextPage($idx) {

        $res = $this->select('id')->orderByDesc('top')->orderByDesc('id')->get();

        if (!empty($res)) {
            foreach ($res as $index => $item) {
                if($item->id == $idx) { return empty($res[$index-1]) ? null : $res[$index-1]; }
            }
        }

        return null;

    }


    /* ----------------------------------------------------  */
    protected function admin_list() {
        return $this->join('users', 'users.id', '=', 'board_notice.ref_admin_id')
            ->select(
            'board_notice.id',
            'board_notice.top',
            'board_notice.title',
            'users.name',
            'board_notice.content',
            'board_notice.created_at'
        )->where('board_notice.status', 0)->orderByDesc('board_notice.id')->get();
    }

    protected function admin_detail($id) {
        return $this->select(
            'top',
            'title',
            'content',
        )->where('status', 0)->where('id', $id)->first();
    }


    //
    protected function create($data, $admin_id) {

        return $this->insertGetId([
            'top' => $data['top'],
            'ref_admin_id' => $admin_id,
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

    }


    //
    protected function updateContent($data, $admin_id) {

        return $this->where('id', $data['id'])
            ->update([
                'top' => $data['top'],
                'ref_admin_id' => $admin_id,
                'title' => $data['title'],
                'content' => $data['content'],
            ]);

    }


    //
    protected function deleteContent($id, $admin_id) {

        return $this->where('id', $id)->update([
            'status' => 1,
            'ref_admin_id' => $admin_id,
        ]);

    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
