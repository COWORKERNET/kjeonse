<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContestFiles extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'contest_files';


    // Contest Detail
    protected function getFiles($ref_contest_id) {
        return $this->select(
            'type',
            'originFileName',
            'fileAddress'
        )->where('status', 0)->where('ref_contest_id', $ref_contest_id)->orderByDesc('type')->get();
    }


    // Contest Register Upload Files
    protected function uploadFiles($data, $admin_id, $contestId) {

        if(!empty($data['main_image_file'])) {

            $file = $data['main_image_file'];

            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/contest/', $fileName);
            $url = "https://api.kjeonse.com/storage/contest/";

            $this->insert([
                'ref_contest_id' => $contestId,
                'ref_admin_id' => $admin_id,
                'type' => 0,
                'size' => $fileSize,
                'originFileName' => $originFileName,
                'fileAddress' => $url . $fileName,
            ]);
        }

        if(!empty($data['slider_image_files'])) {

            $files = $data['slider_image_files'];

            foreach ($files as $file) {
                $originFileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = (now()->timestamp) . '_' . $originFileName;
                $path = $file->storeAs('public/contest/', $fileName);
                $url = "https://api.kjeonse.com/storage/contest/";

                $this->insert([
                    'ref_contest_id' => $contestId,
                    'ref_admin_id' => $admin_id,
                    'type' => 1,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url . $fileName,
                ]);
                usleep(300);
            }
        }

        if(!empty($data['content_files'])) {

            $files = $data['content_files'];

            foreach ($files as $file) {
                $originFileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = (now()->timestamp) . '_' . $originFileName;
                $path = $file->storeAs('public/contest/', $fileName);
                $url = "https://api.kjeonse.com/storage/contest/";

                $this->insert([
                    'ref_contest_id' => $contestId,
                    'ref_admin_id' => $admin_id,
                    'type' => 2,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url . $fileName,
                ]);

                usleep(300);
            }

        }


    }


    // Contest Update Upload Files
    protected function updateFiles($data, $admin_id) {

        if(!empty($data['remove_files'])) {
            $this->whereIn('id', $data['remove_files'])->update([
                'status' => 1,
                'ref_admin_id' => $admin_id
            ]);
        }

        if(!empty($data['main_image_file'])) {

            $this->where('ref_contest_id', $data['id'])->where('type', 0)->update([
               'status' => 1,
            ]);

            $file = $data['main_image_file'];

            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/contest/', $fileName);
            $url = "https://api.kjeonse.com/storage/contest/";

            $this->insert([
                'ref_contest_id' => $data['id'],
                'ref_admin_id' => $admin_id,
                'type' => 0,
                'size' => $fileSize,
                'originFileName' => $originFileName,
                'fileAddress' => $url . $fileName,
            ]);
        }

        if(!empty($data['slider_image_files'])) {

            $files = $data['slider_image_files'];

            foreach ($files as $file) {
                $originFileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = (now()->timestamp) . '_' . $originFileName;
                $path = $file->storeAs('public/contest/', $fileName);
                $url = "https://api.kjeonse.com/storage/contest/";

                $this->insert([
                    'ref_contest_id' => $data['id'],
                    'ref_admin_id' => $admin_id,
                    'type' => 1,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url . $fileName,
                ]);
                usleep(300);
            }
        }

        if(!empty($data['content_files'])) {

            $files = $data['content_files'];

            foreach ($files as $file) {
                $originFileName = $file->getClientOriginalName();
                $fileSize = $file->getSize();
                $fileName = (now()->timestamp) . '_' . $originFileName;
                $path = $file->storeAs('public/contest/', $fileName);
                $url = "https://api.kjeonse.com/storage/contest/";

                $this->insert([
                    'ref_contest_id' => $data['id'],
                    'ref_admin_id' => $admin_id,
                    'type' => 2,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url . $fileName,
                ]);

                usleep(300);
            }

        }

    }


    //
    protected function admin_detail_get_main_image_files($contest_id) {
        return $this->select(
            'id',
            'originFileName',
            'fileAddress',
        )
            ->where('status', 0)
            ->where('type', 0)
            ->where('ref_contest_id', $contest_id)
            ->first();
    }


    //
    protected function admin_detail_get_files($contest_id, $type) {
        return $this->select(
            'id',
            'originFileName',
            'fileAddress',
        )
            ->where('status', 0)
            ->where('type', $type)
            ->where('ref_contest_id', $contest_id)
            ->get();
    }


    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
