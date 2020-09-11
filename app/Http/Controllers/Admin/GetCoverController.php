<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cover;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
class GetCoverController extends Controller
{
    const saveFileType = 'jpg';
    protected function index()
    {
        $data['items'] = Cover::orderBy('id', 'desc')->paginate(20);
        return view('getcover',$data);
    }
    /**
    * Lấy ảnh và cover thành các thumbnail
    *
    * @param $request
    */
    public function postCover(Request $request)
    {

        $fileConfig = config('filecover');
        if (!empty($fileConfig['fileSize'])) {
            if ($request->type == 'again') {    // Nếu quét lại từ đầu
                $results = collect();
                $this->getDirContents($request->ulr, $results, 'again');
            } else {
                $results = Cover::where([
                    ['file', 'like', str_replace("\\", "/", $request->ulr) . '%'],
                    ['avatar', null]
                ])->get();
            }
            if (count($results) != 0) {
                foreach ($results as $image) {
                    $file = pathinfo($image->file);
                    $start = strpos($image->file, "/") + 1;
                    $end = strripos($image->file, "/");
                    $path = substr($image->file, $start, $end - $start + 1);
                    dump($image->file);
                    //$pathOut = 'public\Image Cover\\' . str_replace("/", "\\", $path);
                    //$result = $this->thumbGenerator(str_replace("/", "\\", $image->file), $file['filename'], $file['extension'], $fileConfig['fileSize'], $pathOut);
                    try {
                        dump($image->file);
                        $pathOut = 'public\Image Cover\\' . str_replace("/", "\\", $path);
                        $result = $this->thumbGenerator(str_replace("/", "\\", $image->file), $file['filename'], $file['extension'], $fileConfig['fileSize'], $pathOut);
                    } catch (\Exception $e) {
                        $result = false;
                    }
                    if ($result !== false) {
                        $image->update([
                            'avatar' => $result,
                            'updated_at' => Carbon::now(),
                        ]);
                    }
                }
            }
        }
        return redirect('admin/get-cover')->with('success', 'Cover thành công');
    }
    /**
     * Lấy danh sách ảnh trong thư mục
     *
     * @param string $dir Đường dẫn thư mục cần quét
     * @param collection $results Mảng lưu kết quả
     * @return collection $results
     */
    function getDirContents($dir, &$results)
    {
        set_time_limit(0);
        $fileConfig = config('filecover');
        if (!empty($fileConfig['fileTypes'])) {
            $files = scandir($dir);
            foreach ($files as $value) {
                $path = realpath($dir . DIRECTORY_SEPARATOR . $value);
                if (!is_dir($path)) {
                    // Nếu tìm thấy rồi thì thoát vòng lặp
                    foreach ($fileConfig['fileTypes'] as $val) {
                        if (preg_match('/\.' . $val . '$/', strtolower($path))) {
                            $image = Cover::firstOrCreate(['file' => str_replace("\\", "/", $path)]);

                            // Nếu chưa được gen hoặc đã bị chỉnh sửa
                            if (($image->avatar == null) || ($image->updated_at < date('Y-m-d H:i:s', filemtime($path)))) {
                                $results->push($image);
                            }
                            break;
                        }
                    }
                } elseif ($value != "." && $value != "..") {
                    $this->getDirContents($path, $results);
                }
            }
        }
        return  $results;
    }
    /**
     * Tạo thumbnail cho các ảnh quét được
     */
    function thumbGenerator($dir, $tmpName, $fileType, $size, $pathOut)
    {
        $saveFileType = self::saveFileType;
        $imagePath = str_replace('\\','/',$dir);
        $image = new \Imagick();
        switch ($fileType) {
            case 'psd':
                $imagePath = $imagePath . '[0]'; //lấy layout đầu tiên
                $image->readImage($imagePath);
                break;
            case 'cdr':
                $output = trim(preg_replace('/(.+).cdr/', ' $1', $imagePath) . '.svg', ' ');
                // $this->convertFile($imagePath,$output);
                system('uniconvertor ' . $imagePath . ' ' . $output);
                if(!empty(file_get_contents($output))){
                    $image->readImageBlob(file_get_contents($output));
                }
                break;
            case 'pdf':
                $imagePath = $imagePath . '[0]';
                $image->readImage($imagePath);
                break;
            case 'ai':
                $imagePath = $imagePath . '[0]'; //
                $image->readImage($imagePath);
                break;
            case 'eps':
                $imagePath = $imagePath . '[0]'; //lay
                $image->readImage($imagePath);
                break;
            default:
                $image->readImage($imagePath);
                break;
        }
        $image->setImageCompressionQuality(70); //độ phân giải ảnh càng cao càng đẹp
        foreach ($size as $type => $value) {
            $maxWidth = $value['width'];
            $maxHeight = $value['height'] ?? ($image->getImageHeight() * $maxWidth / $image->getImageWidth());
            $image->thumbnailImage($maxWidth, $maxHeight);
            $image->setImageFormat("jpeg");
            Storage::disk('local')->put($pathOut . $tmpName . '-' . $type . "." . $saveFileType, $image->getImageBlob());
        }
        $image->clear();
        $image->destroy();
        if (!empty($output)  && file_exists($output)) {
            unlink($output);
        }
        return $pathOut . $tmpName . '-' . $type . "." . $saveFileType;
    }

}
