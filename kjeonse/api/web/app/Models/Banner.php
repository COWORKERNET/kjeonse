<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'banners';


    // [c] User Main
    protected function main() {
        return $this->select(
            'title',
            'description',
            'fileAddress'
        )->where('status', '=', 0)->get();
    }


    // [c] admin banner list
    protected function list() {
        return $this->select(
            'id',
            'title',
            'description',
            'fileAddress',
        )->where('status', 0)
            ->orderByDesc('id')->paginate(8);
    }


    // [c] admin banner create
    protected function create($data, $admin_id) {

        $file = $data['file'];

        $originFileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileName = (now()->timestamp) . '_' . $originFileName;
        $path = $file->storeAs('public/banner/', $fileName);
        $url = "https://api.kjeonse.com/storage/banner/";

        return $this->insertGetId([
            'ref_admin_id' => $admin_id,
            'title' => $data['title'],
            'description' => $data['description'],
            'size' => $fileSize,
            'originFileName' => $originFileName,
            'fileAddress' => $url.$fileName,
        ]);

    }


    // [c]
    protected function updateContent($data, $admin_id) {

        $id             = $data['id'];
        $title          = $data['title'];
        $content_url    = $data['description'];

        $query = $this->where('id', $id);

        if(!empty($data['file'])) {

            $file           = $data['file'];
            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/banner/', $fileName);
            $url = "https://api.kjeonse.com/storage/banner/";

            return $query->update([
                'ref_admin_id' => $admin_id,
                'title' => $title,
                'description' => $content_url,
                'size' => $fileSize,
                'originFileName' => $originFileName,
                'fileAddress' => $url.$fileName,
            ]);
        } else {
            return $query->update([
                'ref_admin_id' => $admin_id,
                'title' => $title,
                'description' => $content_url,
            ]);
        }

    }


    // [c]
    protected function deleteContent($id) { return $this->where('id', $id)->update([ 'status' => 1, ]); }


    // [c]
    protected function detail($id) {
        return $this->select(
            'id',
            'title',
            'description',
            'originFileName',
            'fileAddress',
        )->where('id', $id)->first();
    }



    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
