<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserEstateContractor extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_estate_document_contractor';


    // 문서 리스트
    protected function list($ref_user_estate_register_list_id, $user_id) {

        return $this->select(
            DB::raw('(CASE WHEN user_estate_document_contractor.type = 0 THEN "임대인" WHEN user_estate_document_contractor.type = 1 THEN "임차인" else "관리자" end) as writerType'),
            DB::raw('(CASE WHEN user_estate_document_contractor.ref_writer_user_id = '. $user_id .' THEN 1 else 0 end) as writer'),
            'user_estate_document_contractor.id',
            'user_estate_document_contractor.title',
            'user_estate_document_contractor.created_at'
        )
        ->where('user_estate_document_contractor.status', 0)
        ->where('user_estate_document_contractor.ref_user_estate_register_list_id', $ref_user_estate_register_list_id)
        ->get();

    }


    // 문서 상세보기
    protected function detail($id) {

        $data = [];

        $data['detail'] = $this->select(
            'id',
            'title',
            'content',
            'created_at',
        )->where('status', 0)->where('id', $id)->first();

        // type. 0: document, 1: question, 2: admin notice
        $data['files'] = \App\Models\UserEstateFiles::getFiles(0, $id);

        return $data;

    }


    // 문서 삭제
    protected function remove($id) { $this->where('id', $id)->update([ 'status' => 1, ]); }


    // 문서 업데이트
    protected function setUpdate($id, $title, $content) {
        $this->where('id', $id)->update([
            'title' => $title,
            'content' => $content,
        ]);
    }


    // 문서 등록 Type 0: 임대인, 1:임차인, 2:관리자
    protected function register($request, $user_id) {

        $type = \App\Models\UserEstate::getTypeUser($request['estate_id'], $user_id);

        return $this->insertGetId([
            'ref_writer_user_id' => $user_id,
            'ref_user_estate_register_list_id' => $request['estate_id'],
            'title'     => $request['title'],
            'content'   => $request['content'],
            'type'      => $type,
        ]);
    }

    // 문서 등록자 체크
    protected function documentPolicyCheck($id, $user_id) {
        return $this->where('status', 0)->where('id', $id)->where('ref_writer_user_id', $user_id)->count();
    }

    /* ----------------------------------------------------------- */
    protected function admin_detail_contest($estate_id) {
        return $this->join(
            'users', 'users.id', '=', 'user_estate_document_contractor.ref_writer_user_id'
        )
            ->select(
                'user_estate_document_contractor.id',
                'user_estate_document_contractor.title',
                'users.name',
                'user_estate_document_contractor.created_at',
        )
            ->where('user_estate_document_contractor.status', 0)
            ->where('user_estate_document_contractor.ref_user_estate_register_list_id', $estate_id)
            ->get();
    }

    //
    protected function admin_register_content($data, $admin_id) {
        return $this->insertGetId([
            'ref_writer_user_id' => $admin_id,
            'ref_user_estate_register_list_id' => $data['eid'],
            'title' => $data['title'],
            'content' => $data['content'],
            'type' => 2,
        ]);
    }

    //
    protected function admin_update_content($data, $admin_id) {

        return $this->where('id', $data['did'])
            ->where('ref_user_estate_register_list_id', $data['eid'])
            ->update([
                'title' => $data['title'],
                'content'=> $data['content'],
                'type' => 2,
            ]);

    }

    //
    protected function admin_delete_content($estate_id, $document_id) {

        return $this->where('id', $document_id)
            ->where('ref_user_estate_register_list_id', $estate_id)
            ->update([
                'status' => 1,
            ]);

    }

    //
    protected function admin_detail_content($estate_id, $document_id) {
        return $this->select(
            'id',
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
