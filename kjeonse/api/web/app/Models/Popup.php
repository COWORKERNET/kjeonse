<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Popup extends Model
{
    use HasFactory;

    protected $connection = 'mysql';

    protected $table = 'popup';


    // [c]
    protected function list() {
        return $this->select(
            'id',
            'title',
            'url',
            'fileAddress',
        )->where('status', 0)
            ->orderByDesc('id')
            ->paginate(8);
    }


    // [c]
    protected function create($data) {

        $file = $data['file'];

        $originFileName = $file->getClientOriginalName();
        $fileSize = $file->getSize();
        $fileName = (now()->timestamp) . '_' . $originFileName;
        $path = $file->storeAs('public/popup/', $fileName);
        $url = "https://api.kjeonse.com/storage/popup/";

        return $this->insertGetId([
            'title' => $data['title'],
            'url' => $data['url'],
            'size' => $fileSize,
            'originFileName' => $originFileName,
            'fileAddress' => $url.$fileName,
        ]);

    }


    // [c]
    protected function deleteContent($id) { return $this->where('id', $id)->update([ 'status' => 1, ]); }


    // [c]
    protected function updateContent($data) {

        $id             = $data['id'];
        $title          = $data['title'];
        $content_url    = $data['url'];

        $query = $this->where('id', $id);

        if(!empty($data['file'])) {
            $file = $data['file'];
            $originFileName = $file->getClientOriginalName();
            $fileSize = $file->getSize();
            $fileName = (now()->timestamp) . '_' . $originFileName;
            $path = $file->storeAs('public/popup/', $fileName);
            $url = "https://api.kjeonse.com/storage/popup/";

            return $query->update([
                'title' => $title,
                'url' => $content_url,
                'size' => $fileSize,
                'originFileName' => $originFileName,
                'fileAddress' => $url.$fileName,
            ]);
        } else {
            return $query->update([
                'title' => $title,
                'url' => $content_url,
            ]);
        }

    }


    // [c]
    protected function detail($id) {
        return $this->select(
            'id',
            'title',
            'url',
            'originFileName',
            'fileAddress',
        )->where('status', 0)->where('id', $id)->first();
    }


    //
    protected function serializeDate(\DateTimeInterface $date): string { return $date->format('Y-m-d H:i:s'); }
}
