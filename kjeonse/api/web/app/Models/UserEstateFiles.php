<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class UserEstateFiles extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'user_estate_files';


    // type 0: document, 1: question, 2:admin notice
    protected function getFiles($type, $ref_estate_id) {
        return $this->select(
            'id',
            'originFileName',
            'fileAddress',
        )
        ->where('status', 0)->where('type', $type)->where('ref_estate_id', $ref_estate_id)
        ->get();
    }


    // 파일 업데이트
    protected function setUpdateFiles($type, $id, $files) {

        foreach ($files as $file) {

            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/estate/contractor/', $fileName);
            $url = "https://api.kjeonse.com/storage/estate/contractor/";

            $this->insert([
                'type' => 0,
                'ref_estate_id' => $id,
                'size' => $fileSize,
                'originFileName' => $originFileName,
                'fileAddress' => $url.$fileName,
            ]);

            usleep(100);
        }

    }


    //
    protected function user_contractor_upload_files($request, $document_id, $type) {

        if(!empty($request['files'])) {

            foreach ($request['files'] as $file) {

                $originFileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = (now()->timestamp) . '_' . $originFileName;
                $path = $file->storeAs('public/estate/', $fileName);
                $url = "https://api.kjeonse.com/storage/estate/";

                $this->insert([
                    'type' => $type,
                    'ref_estate_id' => $document_id,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url.$fileName,
                ]);

                usleep(100);
            }

        }

    }


    //
    protected function user_question_upload_files($files, $document_id) {

        if(!empty($files)) {

            foreach ($files as $file) {

                $originFileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = (now()->timestamp) . '_' . $originFileName;
                $path = $file->storeAs('public/question/', $fileName);
                $url = "https://api.kjeonse.com/storage/question/";

                $this->insert([
                    'type' => 1,
                    'ref_estate_id' => $document_id,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url.$fileName,
                ]);

                usleep(100);
            }

        }

    }


    // 파일 삭제
    protected function removeFiles($type, $files) {
        $this->where('status', 0)
            ->where('type', $type)
            ->whereIn('id', $files)
            ->update([
            'status' => 1,
        ]);
    }

    /* ------------------------------------------------------------------ */
    protected function admin_upload_files($estate_id, $files, $type) {

        foreach ($files as $file) {

            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/estate/', $fileName);
            $url = "https://api.kjeonse.com/storage/estate";

            $this->insert([
                'type' => $type,
                'ref_estate_id' => $estate_id,
                'size' => $fileSize,
                'originFileName' => $fileName,
                'fileAddress' => $url.$fileName,
            ]);

            usleep(200);
        }

    }

    //
    protected function admin_update_files($data, $type) {

        // remove files check
        if (!empty($data['remove_files'])) {

            foreach ($data['remove_files'] as $remove) {

                $this->where('id', $remove)
                    ->where('type', $type)
                    ->update([
                        'status' => 1,
                    ]);

            }
        }

        // insert files check
        if(!empty($data['files'])) {

            foreach ($data['files'] as $file) {

                $originFileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = (now()->timestamp) . '_' . $originFileName;
                $path = $file->storeAs('public/estate/', $fileName);
                $url = "https://api.kjeonse.com/storage/estate/";

                $this->insert([
                    'type' => $type,
                    'ref_estate_id' => $data['did'],
                    'size' => $fileSize,
                    'originFileName' => $fileName,
                    'fileAddress' => $url.$fileName
                ]);

                usleep(200);
            }

        }

    }

    //
    protected function admin_get_files($document_id, $type) {
        return $this->select(
            'id',
            'originFileName',
            'fileAddress',
        )
            ->where('status', 0)
            ->where('type', $type)
            ->where('ref_estate_id', $document_id)
            ->get();
    }

    //
    protected function admin_delete_estate_all_files_remove($document_id, $type) {
        return $this->where('ref_estate_id', $document_id)->where('type', $type)->update([
            'status' => 1,
        ]);
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
