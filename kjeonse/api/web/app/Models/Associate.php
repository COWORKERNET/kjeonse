<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Models\AssociateFiles;

class Associate extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'associate';


    // 메인 화면 협력사 리스트
    protected function main() {
        return $this->select(
                'fileAddress',
            )
            ->where('status', '=', 0)
            ->get();
    }


    // [c]
    protected function list() {
        return $this->select(
            'id',
            'title',
            'fileAddress'
        )->where('status', 0)->orderByDesc('id')->paginate(8);
    }


    // [c]
    protected function create($data, $admin_id) {

        $file = $data['file'];

        $originFileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileName = (now()->timestamp) . '_' . $originFileName;
        $path = $file->storeAs('public/associate/', $fileName);
        $url = "https://api.kjeonse.com/storage/associate/";

        return $this->insertGetId([

            'title' => $data['title'],
            'ref_admin_id' => $admin_id,
            'size' => $fileSize,
            'originFileName' => $originFileName,
            'fileAddress' => $url.$fileName,

        ]);

    }


    // [c]
    protected function updateContent($data, $admin_id) {

        $id             = $data['id'];
        $title          = $data['title'];

        $query = $this->where('id', $id);

        if(!empty($data['file'])) {

            $file = $data['file'];

            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/associate/', $fileName);
            $url = "https://api.kjeonse.com/storage/associate/";

            return $query->update([
                'ref_admin_id' => $admin_id,
                'title' => $title,
                'size' => $fileSize,
                'originFileName' => $originFileName,
                'fileAddress' => $url.$fileName,
            ]);
        } else {
            return $query->update([
                'ref_admin_id' => $admin_id,
                'title' => $title,
            ]);
        }

    }


    // [c]
    protected function deleteContent($id) {
        return $this->where('id', $id)->update([
            'status' => 1
        ]);
    }


    // [c]
    protected function detail($id) {
        return $this->select(
            'id',
            'title',
            'originFileName',
            'fileAddress'
        )
            ->where('status', 0)
            ->where('id', $id)
            ->first();
    }

    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
