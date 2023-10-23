<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserEstateQuestion extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_estate_question_list';


    // 전세 현황 관리 시설 관리문의 리스트
    protected function list($ref_user_estate_register_list_id, $user_id) {

        // 0: 처리요청, 1: 처리보류, 2: 승인완료, 3: 관리자
        return $this->join('user_estate_question_category as ueqc', 'ueqc.id', '=', 'user_estate_question_list.ref_category_question_id')

        ->select(
            'user_estate_question_list.id',
            'ueqc.title as category',
            'user_estate_question_list.title',
            'user_estate_question_list.type',
            'user_estate_question_list.created_at'
        )
        ->where('user_estate_question_list.status', 0)
        ->where('user_estate_question_list.ref_user_estate_register_list_id', $ref_user_estate_register_list_id)
        ->where(function($query) use($user_id) {
            $query->where(function ($query) {
                $query->where('user_estate_question_list.ref_admin_approve', 1);
            })
            ->orWhere(function ($query) use($user_id) {
                $query->where('user_estate_question_list.ref_writer_user_id', $user_id);
            });
        })
        ->orderByDesc('id')
        ->get();
    }


    // 전세 현황 관리 시설 관리문의 상세보기
    protected function detail($id) {

        // title, content, state type, file

        $data = [];
        $data['detail'] = $this->select(
            'title',
            'content',
            'type',
            'created_at'
        )
        ->where('status', 0)
        ->where('id', $id)
        ->first();

        $data['files'] = \App\Models\UserEstateFiles::getFiles(1, $id);

        return $data;

    }


    //
    protected function register($request, $user_id) {

        // return $request['files'][0]->getClientOriginalName();

        return $this->insertGetId([
            'ref_category_question_id' => $request['category_id'],
            'ref_writer_user_id' => $user_id,
            'ref_user_estate_register_list_id' => $request['estate_id'],
            'title' => $request['title'],
            'content' => $request['content'],
            'type' => 0,
        ]);
    }


    // 문서 삭제
    protected function remove($id) { $this->where('id', $id)->update([ 'status' => 1, ]); }


    // 전세 현황 관리 시설 관리문의 상세보기 임대인 상태 변경
    protected function updateStatus($category_id, $ref_question_id) {
        return $this->where('id', $ref_question_id)->update([
            'type' => $category_id,
        ]);
    }


    /* ----------------------------------------------------------- */
    protected function admin_detail_contest($estate_id) {
        return $this->join(
            'users', 'users.id', '=', 'user_estate_question_list.ref_writer_user_id'
        )->join(
            'user_estate_question_category', 'user_estate_question_category.id', '=', 'user_estate_question_list.ref_category_question_id'
        )
            ->select(
                'user_estate_question_list.id',
                'user_estate_question_category.title as category_title',
                'user_estate_question_list.title',
                'user_estate_question_list.type',
                'users.name',
                'user_estate_question_list.created_at',
            )
            ->where('user_estate_question_list.status', 0)
            ->where('user_estate_question_list.ref_user_estate_register_list_id', $estate_id)
            ->get();
    }


    protected function admin_detail_content($estate_id, $document_id) {
        return $this->select(
            'id',
            'ref_admin_approve',
            'ref_category_question_id as category',
            'title',
            'content',
            'type',
        )
            ->where('status', 0)
            ->where('id', $document_id)
            ->where('ref_user_estate_register_list_id', $estate_id)
            ->first();
    }

    //
    protected function admin_update_content($data, $admin_id) {

        return $this->where('id', $data['did'])->where('ref_user_estate_register_list_id', $data['eid'])->update([
            'ref_admin_id' => $admin_id,
            'ref_admin_approve' => $data['ref_admin_approve'],
            'ref_category_question_id' => $data['category'],
            'title' => $data['title'],
            'content' => $data['content'],
            'type' => $data['type']
        ]);

    }

    //
    protected function admin_delete_content($estate_id, $document_id) {
        return $this->where('id', $document_id)->where('ref_user_estate_register_list_id', $estate_id)->update([
            'status' => 1,
        ]);
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
