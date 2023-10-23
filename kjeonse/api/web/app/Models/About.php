<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class About extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'about';

    // main sector 2 about us Image
    protected function show() {
        // type 0: pc, 1: mobile
        return $this->select('type', 'fileAddress')->where('status', '=', 0)->get();
    }


    // [c]
    protected function admin_list() {
        return $this->select(
            'id',
            'type',
            'fileAddress'
        )->where('status', 0)->get();
    }


    // [c]
    protected function updateContent($data, $admin_id) {

        $query = null;

        if(!empty($data['pc_file'])) {

            $file = $data['pc_file'];

            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/about/', $fileName);
            $url = "https://api.kjeonse.com/storage/about/";

            if(empty($data['pc_id'])) {

                $query = $this->insertGetId([
                    'ref_admin_id' => $admin_id,
                    'type' => 0,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url . $fileName,
                ]);

            } else {

                $query = $this->where('id', $data['pc_id']);
                $query->update([
                    'ref_admin_id' => $admin_id,
                    'type' => 0,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url . $fileName,
                ]);
            }
        }


        if(!empty($data['mb_file'])) {

            $file = $data['mb_file'];

            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/about/', $fileName);
            $url = "https://api.kjeonse.com/storage/about/";

            if(empty($data['mb_id'])) {

                $query = $this->insertGetId([
                    'ref_admin_id' => $admin_id,
                    'type' => 1,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url . $fileName,
                ]);

            } else {

                $query = $this->where('id', $data['mb_id']);

                $query->update([
                    'ref_admin_id' => $admin_id,
                    'type' => 1,
                    'size' => $fileSize,
                    'originFileName' => $originFileName,
                    'fileAddress' => $url . $fileName,
                ]);
            }
        }

        return $query;
    }

    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
