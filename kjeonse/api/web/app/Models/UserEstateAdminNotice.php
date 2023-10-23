<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserEstateAdminNotice extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_estate_admin_notice_list';


    // 전세 부동산 관리 - 관리자 공지 : 수신자 Type 0:임대인, 1: 임차인, 2: 임대인/임차인
    protected function list($ref_estate_list_id, $user_id) {

        $lessor_check = \App\Models\UserEstate::getIsMyEstateCheck($ref_estate_list_id, $user_id);

        $res = $this->select(
            'id',
            DB::raw('(CASE WHEN type = 0 THEN "임대인" WHEN type = 1 THEN "임차인" WHEN type = 2 THEN "임대인/임차인" else "-" end) as target'),
            'title',
            'created_at',
        )->where('status', 0)
        ->where('ref_user_estate_register_list_id', $ref_estate_list_id);

        if($lessor_check) {
            $res->where('type', '!=', 1);
        } else {
            $res->where('type', '!=', 0);
        }

        $res->orderBy('id');

        return $res->get();
    }


    // 전세 부동산 관리 - 관리자 공지 상세보기
    protected function detail($id) {
        return $this->select(
            'id',
            'title',
            'content',
            'created_at',
        )->where('status', 0)
        ->where('id', $id)
        ->orderBy('id')
        ->first();
    }


    /* ------------------------------------------------- */
    protected function admin_detail_contest($estate_id) {
        return $this->join(
            'users', 'users.id', '=', 'user_estate_admin_notice_list.ref_writer_user_id'
        )->select(
            'user_estate_admin_notice_list.id',
            'user_estate_admin_notice_list.type',
            'user_estate_admin_notice_list.title',
            'users.name',
            'user_estate_admin_notice_list.created_at',
        )
            ->where('user_estate_admin_notice_list.status', 0)
            ->where('user_estate_admin_notice_list.ref_user_estate_register_list_id', $estate_id)
            ->orderByDesc('user_estate_admin_notice_list.created_at')
            ->get();
    }

    //
    protected function admin_register_content($data, $admin_id) {

        return $this->insertGetId([
                'ref_writer_user_id' => $admin_id,
                'ref_user_estate_register_list_id' => $data['eid'],
                'title' => $data['title'],
                'content' => $data['content'],
                'type' => $data['type'],
            ]);

    }

    //
    protected function admin_update_content($data, $admin_id) {

        return $this->where('id', $data['did'])->where('ref_user_estate_register_list_id', $data['eid'])->update([
            'ref_writer_user_id' => $admin_id,
            'title' => $data['title'],
            'content' => $data['content'],
        ]);

    }

    //
    protected function admin_delete_content($estate_id, $document_id) {
        return $this->where('id', $document_id)
            ->where('ref_user_estate_register_list_id', $estate_id)
            ->update([
                'status' => 1
            ]);
    }

    //
    protected function admin_detail_content($estate_id, $document_id) {

         return $this->select(
             'type',
             'title',
             'content',

         )
             ->where('status', 0)
             ->where('id', $document_id)
             ->where('ref_user_estate_register_list_id', $estate_id)
             ->first();

    }


    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }

}
