<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestInfo extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'contest_info';

    //
    protected function detail($idx) {
        return $this->select(
                    'title',
                    'content',
                )->where('status', 0)
                ->where('ref_contest_id', $idx)
                ->get();
    }

    //
    protected function create($data, $admin_id, $contest_id) {

        if(!empty($data['info_row'])) {
            foreach ($data['info_row'] as $info) {
                $jsonInfo = json_decode($info, true);
                $this->insert([
                    'ref_contest_id' => $contest_id,
                    'ref_admin_id' => $admin_id,
                    'title' => $jsonInfo['title'],
                    'content' => $jsonInfo['content']
                ]);
            }
        }

    }

    //
    protected function updateInfo($data, $admin_id) {

        $whereInArray = [];

        if(!empty($data['info_row'])) {

            foreach ($data['info_row'] as $info) {

                $jsonInfo = json_decode($info, true);

                $id = null;

                if($jsonInfo['key'] == 0) {

                    $id = $this->insertGetId([
                        'ref_contest_id' => $data['id'],
                        'ref_admin_id' => $admin_id,
                        'title' => $jsonInfo['title'],
                        'content' => $jsonInfo['content']
                    ]);

                } else {

                    $id = $jsonInfo['key'];

                    $this->where('id', $jsonInfo['key'])->update([
                        'ref_contest_id' => $data['id'],
                        'ref_admin_id' => $admin_id,
                        'title' => $jsonInfo['title'],
                        'content' => $jsonInfo['content']
                    ]);
                }

                array_push($whereInArray, $id);
            }

            // 기존 데이터 삭제
            $this->where('ref_contest_id', $data['id'])->whereNotIn('id', $whereInArray)->update([
                'status' => 1,
            ]);

        } else {

            $this->where('ref_contest_id', $data['id'])->update([
                'status' => 1,
            ]);

        }

    }

    //
    protected function admin_detail_info($contest_id) {
        return $this->select(
            'id as key',
            'title',
            'content'
        )->where('status', 0)->where('ref_contest_id', $contest_id)->get();
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
