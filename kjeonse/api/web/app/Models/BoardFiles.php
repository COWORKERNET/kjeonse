<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BoardFiles extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'board_files';

    // Guide, Notice : 이용안내 및 공지사항 첨부파일 가져오기
    protected function getFiles($type, $idx) {
        return $this->select('originFileName', 'fileAddress')->where('status', 0)->where('ref_board_type', $type)->where('ref_board_id', $idx)->get();
    }


    //
    protected function getBoardFiles($id, $type) {
        // type 0: editor, 1: files
        return $this->select(
            'id',
            'originFileName',
            'fileAddress'
        )
            ->where('status', 0)
            ->where('ref_board_id', $id)
            ->where('ref_board_type', $type)
            ->where('type', 1)
            ->get();
    }


    //
    protected function deleteFiles($id, $type) {
        return $this->where('id', $id)
            ->where('ref_board_type', $type)
            ->update([
                'status' => 1,
            ]);
    }


    //
    protected function updateFiles($data, $type, $admin_id) {

        // create files row
        // remove files row

        if(!empty($data['removeFiles'])) {
            $this->whereIn('id', $data['removeFiles'])->update([
                'status' => 1,
            ]);
        }

        if(!empty($data['files'])) {

            foreach ($data['files'] as $file) {

                $originFileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = (now()->timestamp) . '_' . $originFileName;
                $path = $file->storeAs('public/guide/', $fileName);
                $url = "https://api.kjeonse.com/storage/guide/";

                $this->insertGetId([
                    'ref_admin_id' => $admin_id,
                    'ref_board_id' => $data['id'],
                    'ref_board_type' => $type,
                    'type' => 1,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url.$fileName,
                ]);

                usleep(300);

            }
        }

    }


    //
    protected function uploadFiles($data, $detail_id, $admin_id, $type) {

        if (!empty($data['files'])) {

            foreach ($data['files'] as $file) {

                $originFileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = (now()->timestamp) . '_' . $originFileName;

                $url = '';

                if($type == 0) {
                    $path = $file->storeAs('public/guide/', $fileName);
                    $url = "https://api.kjeonse.com/storage/guide/";
                } else {
                    $path = $file->storeAs('public/notice/', $fileName);
                    $url = "https://api.kjeonse.com/storage/notice/";
                }

                $this->insert([
                    'ref_board_id' => $detail_id,
                    'ref_board_type' => $type,
                    'ref_admin_id' => $admin_id,
                    'type' => 1,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url.$fileName,
                ]);

                usleep(300);
            }

        }

    }


    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
