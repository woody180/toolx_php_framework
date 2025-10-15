<?php namespace App\Controllers;

use \Gumlet\ImageResize;
use  App\Engine\Libraries\Validation;

class FileManagerController {

    private $configurations = [];
    private $currentUrl = '';


    public function __construct() {
        $this->configurations = [
            'viewPath' => 'filemanager/filemanager',
            'fileDirectoryName' => 'images/files',
            'extensions' => ['application/zip', 'application/octet-stream', 'multipart/x-zip', 'application/zip-compressed', 'application/x-zip-compressed', 'application/x-zip', 'image/jpeg', 'image/png', 'image/gif', 'image/webp', 'image/bmp', 'audio/mp3', 'audio/mpeg', 'video/mp4', 'application/mp4', 'video/webm', 'audio/webm', 'application/pdf'],
            'validationRules' => [
                'images' => 'max_size[50000000]|ext[jpg,jpeg,JPG,JPEG,gif,bmp,png,webp,mp3,mp4,webm,pdf]'
            ],
            'imageExtensions' => ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp']
        ];
    }



    protected function cacheImages($imagePath, $cachedFile) {
        $ext = strtolower(pathinfo($imagePath, PATHINFO_EXTENSION));
        if (in_array($ext, ['jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp'])) {
            switch ($ext) {
                case 'jpg':
                case 'jpeg':
                    $src = imagecreatefromjpeg($imagePath);
                    // Fix rotation for JPEG using EXIF
                    if (function_exists('exif_read_data')) {
                        $exif = @exif_read_data($imagePath);
                        if (!empty($exif['Orientation'])) {
                            switch ($exif['Orientation']) {
                                case 3:
                                    $src = imagerotate($src, 180, 0);
                                    break;
                                case 6:
                                    $src = imagerotate($src, -90, 0);
                                    break;
                                case 8:
                                    $src = imagerotate($src, 90, 0);
                                    break;
                            }
                        }
                    }
                    break;
                case 'png':
                    $src = imagecreatefrompng($imagePath);
                    break;
                case 'gif':
                    $src = imagecreatefromgif($imagePath);
                    break;
                case 'webp':
                    if (function_exists('imagecreatefromwebp')) {
                        $src = imagecreatefromwebp($imagePath);
                    } else {
                        $src = false;
                    }
                    break;
                case 'bmp':
                    if (function_exists('imagecreatefrombmp')) {
                        $src = imagecreatefrombmp($imagePath);
                    } else {
                        $src = false;
                    }
                    break;
                default:
                    $src = false;
            }
            if ($src) {
                $width = imagesx($src);
                $height = imagesy($src);
                $longSide = max($width, $height);
                $scale = 80 / $longSide;
                $newWidth = (int)($width * $scale);
                $newHeight = (int)($height * $scale);
                $dst = imagecreatetruecolor($newWidth, $newHeight);

                // Preserve transparency for PNG/GIF/WebP
                if (in_array($ext, ['png', 'gif', 'webp'])) {
                    imagealphablending($dst, false);
                    imagesavealpha($dst, true);
                }

                imagecopyresampled($dst, $src, 0, 0, 0, 0, $newWidth, $newHeight, $width, $height);

                switch ($ext) {
                    case 'jpg':
                    case 'jpeg':
                        imagejpeg($dst, $cachedFile, 85);
                        break;
                    case 'png':
                        imagepng($dst, $cachedFile, 6);
                        break;
                    case 'gif':
                        imagegif($dst, $cachedFile);
                        break;
                    case 'webp':
                        if (function_exists('imagewebp')) {
                            imagewebp($dst, $cachedFile, 80);
                        }
                        break;
                    case 'bmp':
                        if (function_exists('imagebmp')) {
                            imagebmp($dst, $cachedFile);
                        }
                        break;
                }
                imagedestroy($src);
                imagedestroy($dst);
            }
        }
    }

    

    // Show all files and folders
    public function index($req, $res, ...$params) {
        
        // Route first parameter is the filemanager home page directory
        // We going to define directory location. What going to be the root directory for file manager
        // If no parameter provided, we going to use default directory from configurations
        // Other parameters of the url are subdirectories
       
        $firstParam = $params[0] ?? null;
        array_splice($params, 0, 1); // Remove first parameter from array, because it's the filemanager route

        $filesPath = dirname(APPROOT) . '/public/assets/' . $this->configurations['fileDirectoryName']; // Path to file manager main directory
        $baseUrl = '/assets/' . $this->configurations['fileDirectoryName'];
        $this->currentUrl = $baseUrl; // This variable is for files and it is not legacy url
        $legacyUrl = baseUrl($firstParam);
        

        if (count($params) > 0) {
            foreach ($params as $param) {
                $safeParam = str_replace(['..', '/', '\\'], '', $param);
                $this->currentUrl .= '/' . urlencode($safeParam);
                $filesPath .= '/' . $safeParam;
                $legacyUrl .= '/' . urlencode($safeParam);
            }
        }
   
        // Handle directories with spaces in their names
        $filesPath = str_replace('%20', ' ', $filesPath);
    
        // Check if directory exists
        if (!is_dir($filesPath)) abort(['code' => 403, 'text' => 'File manager directory does not exist. Please check configurations.']);

        // Getting all files and folders from directory main directory based on configurations extensions
        // Collecting all files and folders in $items array
        $items = []; 
        $dirIterator = new \DirectoryIterator($filesPath);
        
        foreach ($dirIterator as $fileinfo) {
            if ($fileinfo->isDot()) continue;

            $imageFilePath = explode(dirname(APPROOT) . '/public', $fileinfo->getPathname())[1];

            if ($fileinfo->isDir()) {
                if (strpos($fileinfo->getFilename(), '.') === 0) continue;
                $items[] = [
                    'type' => 'folder',
                    'name' => $fileinfo->getFilename(),
                    'path' => $imageFilePath,
                    'fullPath' => $fileinfo->getPathname(),
                    'mime' => NULL
                ];
            } elseif ($fileinfo->isFile()) {
                $mimeType = mime_content_type($fileinfo->getPathname());
                if (in_array($mimeType, $this->configurations['extensions'])) {

                    // Check if chache directory exists for this directory
                    $cacheDir = $filesPath . '/.cache';
                    if (!is_dir($cacheDir)) mkdir($cacheDir, 0755);


                    // Create cache file if image not exists in cache directory
                    if (in_array(strtolower($fileinfo->getExtension()), $this->configurations['imageExtensions'])) 
                    {
                        $cachedPath = explode($fileinfo->getFilename(), $imageFilePath)[0] . '.cache/' . $fileinfo->getFilename();

                        $items[] = [
                            'type' => 'file',
                            'name' => $fileinfo->getFilename(),
                            'path' => $imageFilePath,
                            'cachedPath' => $cachedPath,
                            'fullPath' => $fileinfo->getPathname(),
                            'mime' => $mimeType
                        ];
                    }


                    if (in_array(strtolower($fileinfo->getExtension()), ['mp3'])) 
                    {
                        $items[] = [
                            'type' => 'audio',
                            'name' => $fileinfo->getFilename(),
                            'path' => $imageFilePath,
                            'fullPath' => $fileinfo->getPathname(),
                            'mime' => $mimeType
                        ];
                    }

                    if (in_array(strtolower($fileinfo->getExtension()), ['mp4', 'webm'])) 
                    {
                        $items[] = [
                            'type' => 'video',
                            'name' => $fileinfo->getFilename(),
                            'path' => $imageFilePath,
                            'fullPath' => $fileinfo->getPathname(),
                            'mime' => $mimeType
                        ];
                    }


                    if (in_array(strtolower($fileinfo->getExtension()), ['pdf'])) 
                    {
                        $items[] = [
                            'type' => 'document',
                            'name' => $fileinfo->getFilename(),
                            'path' => $imageFilePath,
                            'fullPath' => $fileinfo->getPathname(),
                            'mime' => $mimeType
                        ];
                    }


                    if (in_array(strtolower($fileinfo->getExtension()), ['zip'])) 
                    {
                        $items[] = [
                            'type' => 'archive',
                            'name' => $fileinfo->getFilename(),
                            'path' => $imageFilePath,
                            'fullPath' => $fileinfo->getPathname(),
                            'mime' => $mimeType
                        ];
                    }
                }
            }
        }

        // Remove last part of the legacyUrl for back button
        $backUrl = preg_replace('#/[^/]+$#', '', $legacyUrl);
        $backUrl = str_replace('%20', ' ', $backUrl);
        $backUrl = str_replace('%2520', ' ', $backUrl);
        $keyword = explode(baseUrl('') . '/', url_to('FileManagerController@index'))[1];
        if (strpos($backUrl, $keyword) === false) $backUrl = url_to('FileManagerController@index');

        usort($items, function ($a, $b) {
            // First, sort by type: folders first, then files, audio, video, document
            $typeOrder = [
                'folder' => 0,
                'file' => 1,
                'audio' => 2,
                'video' => 3,
                'document' => 4
            ];
            $typeA = $typeOrder[$a['type']] ?? 99;
            $typeB = $typeOrder[$b['type']] ?? 99;
            if ($typeA !== $typeB) {
                return $typeA - $typeB;
            }
            // Then, sort by date (descending, newest first)
            $timeA = filemtime($a['fullPath']);
            $timeB = filemtime($b['fullPath']);
            return $timeB - $timeA;
        });

        return $res->render($this->configurations['viewPath'], [
            'items' => $items, // Loaded files
            'currentUrl' => $this->currentUrl, // URL to the storage where files are stored and in which folder it is right now
            'baseUrl' => $baseUrl, // URL to the storage where files are stored
            'legacyUrl' => $legacyUrl, // URL to file manager route - https://sitename/filemanager
            'backUrl' => $backUrl, // This url is for back button
            'deep_search' => FALSE
        ]);
    }



    // Uploading files
    public function upload($req, $res) {
        $validation = new Validation();

        $errors = $validation
            ->with(['images' => $req->files('images')->show()])
            ->rules([
                'images' => $this->configurations['validationRules']['images'],
            ])
            ->validate();
        
        if (!empty($errors)) {
            // Check if request is AJAX
            if ($req->isAjax()) {
                return $res->send(['status' => 'error', 'message' => $errors]);
            } else {
                setFlashData('error', $errors);
                return $res->redirectBack();
            }
        }

        // Upload files
        try {
           
            $uploadedFiles = $req->files('images')->upload(dirname(APPROOT) . "/public{$req->body('uploadDir')}");

            // Check if chache directory exists for this directory
            $cacheDir = dirname(APPROOT) . "/public" . $req->body('uploadDir') . '/.cache';
            if (!is_dir($cacheDir)) mkdir($cacheDir, 0755);

            foreach($uploadedFiles as $key => $imagePath) {
                $cachedFile = $cacheDir . '/' . basename($imagePath);
                // If Gumlet\ImageResize is available, use it. Otherwise, fallback to vanilla PHP for image resizing.
                if (!file_exists($cachedFile)) {

                    // if (!file_exists($cachedFile)) {
                    //     $image = new ImageResize($imagePath);
                    //     $image->resizeToLongSide(80);
                    //     $image->save($cachedFile);
                    // }
                
                    // PHP alternative for resizing image to long side 80px and fixing rotation based on EXIF
                    $this->cacheImages($imagePath, $cachedFile);
                }
            }
            

        } catch (\Exception $ex) {
            // Check if request is AJAX
            if ($req->isAjax()) {
                return $res->send(['status' => 'error', 'message' => $ex]);
            } else {
                setFlashData('error', $ex);
                return $res->redirectBack();
            }
        }
        
        if ($req->isAjax()) return $res->send(['success' => 'Files has been uploaded successfully']);
        return setFlashData('success', 'Files has been uploaded successfully.');

    }



    // Create folders
    public function makeDirectory($req, $res) {

        $newFolderName = $req->body('dirname');
        $whereToCreate = dirname(APPROOT) . "/public" . $req->body('current_directory') . "/" . $newFolderName;

        // Check if folder already exists
        if (file_exists($whereToCreate)) return $res->send(['error' => 'Such directory already exists.']);
        
        if (mkdir($whereToCreate, 0777, true)) { // 0777 is the permission mode
            return $res->send(["success" => "Directory created successfully at"]);
        }

        return $res->send(["error" => "Failed to create directory."]);
    }



    // Delete selected items
    public function delete($req, $res) {
        
        foreach ($req->body('items') as $item) {
            $directory = dirname(APPROOT) . "/public$item";
            $cachedDirectory = dirname(APPROOT) . "/public/.cache$item";
            if (is_file($directory)) {
                unlink($directory);
                
                $ext = strtolower(pathinfo($directory, PATHINFO_EXTENSION));
                if (in_array($ext, $this->configurations['imageExtensions'])) {
                    if (file_exists($cachedDirectory)) {
                        unlink($cachedDirectory);
                    }
                }
            } else {
                rrmdir($directory);
            }
        }

        return $res->send(['success' => true, 'message' => 'Data has been remove successfully.']);
    }



    // Compress to zip
    public function compress($req, $res) {
        foreach ($req->body('items') as $item) {
            $zipFileName = dirname(APPROOT) . "/public" . $req->body('current_directory') . "/archive_" . date('Ymd_His') . ".zip";
            $zip = new \ZipArchive();
            if ($zip->open($zipFileName, \ZipArchive::CREATE) === TRUE) {
                foreach ($req->body('items') as $item) {
                    $itemPath = dirname(APPROOT) . "/public$item";
                    if (is_file($itemPath)) {
                        $zip->addFile($itemPath, basename($item));
                    } elseif (is_dir($itemPath)) {
                        $files = new \RecursiveIteratorIterator(
                            new \RecursiveDirectoryIterator($itemPath, \FilesystemIterator::SKIP_DOTS),
                            \RecursiveIteratorIterator::SELF_FIRST
                        );
                        foreach ($files as $file) {
                            $filePath = $file->getPathname();
                            $localPath = basename($item) . '/' . substr($filePath, strlen($itemPath) + 1);
                            if ($file->isDir()) {
                                $zip->addEmptyDir($localPath);
                            } else {
                                $zip->addFile($filePath, $localPath);
                            }
                        }
                    }
                }
                $zip->close();
                return $res->send(['success' => true, 'archive' => explode(dirname(APPROOT) . '/public', $zipFileName)[1]]);
            } else {
                return $res->send(['error' => 'Failed to create zip archive.']);
            }
        }
    }



    // Unzip
    public function unzip($req, $res) {
        $extractTo = dirname(APPROOT) . "/public" . $req->body('current_directory');
        $zipFile = $req->body('zip_file');
        $zipFile = reset($zipFile);
        $zipFile = basename($zipFile);
        $zipFile = $extractTo . '/' . $zipFile; // Get first item from array

        $zip = new \ZipArchive();
        if ($zip->open($zipFile) === TRUE) {
            $zip->extractTo($extractTo);
            $zip->close();

            $cacheDir = $extractTo . '/.cache';
            if (!is_dir($cacheDir)) mkdir($cacheDir, 0755);
            $dirIterator = new \DirectoryIterator($extractTo);
            foreach ($dirIterator as $file) {
                if ($file->isFile()) {
                    $ext = strtolower($file->getExtension());
                    if (in_array($ext, $this->configurations['imageExtensions'])) {
                        $cachedFile = $cacheDir . '/' . $file->getFilename();
                        if (!file_exists($cachedFile)) {
                            // Chache images
                            $this->cacheImages($file->getPathname(), $cachedFile);
                        }
                    }
                }
            }


            return $res->send(['success' => true, 'message' => 'Archive extracted successfully.']);
        } else {
            return $res->send(['error' => 'Failed to open zip archive.']);
        }
    }



    // Rename items
    public function rename($req, $res) {
        $oldName = $req->body('old_name');
        $extName = pathinfo($oldName, PATHINFO_EXTENSION);
        $newName = explode('.'.$extName, $req->body('new_name'))[0];
        $newName = $extName ? $newName . '.' . $extName : $newName;

        // If new name has no extension than add _ symbol to name spaces
        if ($extName == '') $newName = preg_replace('/\s+/', '_', $newName);
        $currentDirectory = dirname(APPROOT) . "/public" . $req->body('current_directory');

        // Sanitize new name
        $newName = str_replace(['..', '/', '\\'], '', $newName);

        $oldPath = $currentDirectory . '/' . basename($oldName);
        $newPath = $currentDirectory . '/' . $newName;

        // Check if old file exists
        if (!file_exists($oldPath)) {
            return $res->send(['status' => 'error', 'message' => 'The item you are trying to rename does not exist.']);
        }

        // Check if new file name already exists
        if (file_exists($newPath)) {
            return $res->send(['status' => 'error', 'message' => 'A file or directory with the new name already exists.']);
        }

        // Perform rename operation
        if (rename($oldPath, $newPath)) {
            // Also rename cache file if it's an image
            $ext = strtolower(pathinfo($oldPath, PATHINFO_EXTENSION));
            if (in_array($ext, $this->configurations['imageExtensions'])) {
                $cacheDir = $currentDirectory . '/.cache';
                $oldCachePath = $cacheDir . '/' . basename($oldName);
                $newCachePath = $cacheDir . '/' . $newName;
                if (file_exists($oldCachePath)) {
                    rename($oldCachePath, $newCachePath);
                }
            }

            return $res->send(['success' => 'Item renamed successfully.']);
        } else {
            return $res->send(['status' => 'error', 'message' => 'Failed to rename item.']);
        }
    }



    public function getServerInfo($req, $res) {
        $maxFileUploads = ini_get('max_file_uploads');
        $maxPostSize = ini_get('post_max_size');
        $maxUploadSize = ini_get('upload_max_filesize');

        return $res->send([
            'maxFileUploads' => $maxFileUploads,
            'maxPostSize' => $maxPostSize,
            'maxUploadSize' => $maxUploadSize
        ]);
    }



    public function fileInfo($req, $res) {

        function formatSizeUnits(...$args) {
            $bytes = $args[0];
            if ($bytes >= 1073741824) {
                $bytes = number_format($bytes / 1073741824, 2) . ' GB';
            } elseif ($bytes >= 1048576) {
                $bytes = number_format($bytes / 1048576, 2) . ' MB';
            } elseif ($bytes >= 1024) {
                $bytes = number_format($bytes / 1024, 2) . ' KB';
            } elseif ($bytes > 1) {
                $bytes = $bytes . ' bytes';
            } elseif ($bytes == 1) {
                $bytes = $bytes . ' byte';
            } else {
                $bytes = '0 bytes';
            }
            return $bytes;
        }

        $itemName = query('item_name');
        $itemPath = dirname(APPROOT) . "/public" . $itemName;

        // Check if item exists
        if (!file_exists($itemPath)) return $res->send(['status' => 'error', 'message' => 'The item does not exist.']);

        $info = [];
        $info['name'] = basename($itemName);
        $info['path'] = str_replace(dirname(APPROOT) . '/public', '', $itemPath);
        $info['type'] = is_dir($itemPath) ? 'Directory' : mime_content_type($itemPath);
        $info['size'] = is_file($itemPath) ? formatSizeUnits(filesize($itemPath)) : '-';
        $info['last_modified'] = date("F d Y H:i:s", filemtime($itemPath));
        $info['permissions'] = substr(sprintf('%o', fileperms($itemPath)), -4);
        $info['writable'] = is_writable($itemPath) ? 'Yes' : 'No';
        $info['readable'] = is_readable($itemPath) ? 'Yes' : 'No';

        if (is_file($itemPath)) {
            $info['mime_type'] = mime_content_type($itemPath);
            $info['extension'] = pathinfo($itemPath, PATHINFO_EXTENSION);
        }

        return $res->send(['status' => 'success', 'data' => $info]);
    }



    // Search
    public function search($req, $res) {
        $baseUrl = '/assets/' . $this->configurations['fileDirectoryName'];
        $filesDirectory = dirname(APPROOT) . "/public/assets/" . $this->configurations['fileDirectoryName'];
        $searchTerm = strtolower(query('file_name') ?? ''); // Convert search term to lowercase

        $foundFiles = [];

        // Create a RecursiveDirectoryIterator to iterate through the directory
        $iterator = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($filesDirectory));

        foreach ($iterator as $file) {

            // Skip cached directoreis and files
            if (strpos($file->getPathname(), '.cache') !== false) continue;

            // Get the file name without the extension
            $fileNameWithoutExt = strtolower(pathinfo($file->getFilename(), PATHINFO_FILENAME));
            
            // Check if the file name matches the search term
            if (strpos($fileNameWithoutExt, $searchTerm) !== false) {
                // Check if the file name matches the search term
                if (strpos($fileNameWithoutExt, $searchTerm) !== false) {

                    $mimeType = mime_content_type($file->getPathname());
                    if (in_array($mimeType, $this->configurations['extensions'])) {

                        $imageFilePath = explode(dirname(APPROOT) . '/public', $file->getPathname())[1];

                        // Create cache file if image not exists in cache directory
                        if (in_array(strtolower($file->getExtension()), $this->configurations['imageExtensions'])) 
                        {
                            $cachedPath = explode($file->getFilename(), $imageFilePath)[0] . '.cache/' . $file->getFilename();

                            $foundFiles[] = [
                                'type' => 'file',
                                'name' => $file->getFilename(),
                                'path' => $imageFilePath,
                                'cachedPath' => $cachedPath,
                                'fullPath' => $file->getPathname(),
                                'mime' => $mimeType
                            ];
                        }


                        if (in_array(strtolower($file->getExtension()), ['mp3'])) 
                        {
                            $foundFiles[] = [
                                'type' => 'audio',
                                'name' => $file->getFilename(),
                                'path' => $imageFilePath,
                                'fullPath' => $file->getPathname(),
                                'mime' => $mimeType
                            ];
                        }

                        if (in_array(strtolower($file->getExtension()), ['mp4', 'webm'])) 
                        {
                            $foundFiles[] = [
                                'type' => 'video',
                                'name' => $file->getFilename(),
                                'path' => $imageFilePath,
                                'fullPath' => $file->getPathname(),
                                'mime' => $mimeType
                            ];
                        }


                        if (in_array(strtolower($file->getExtension()), ['pdf'])) 
                        {
                            $foundFiles[] = [
                                'type' => 'document',
                                'name' => $file->getFilename(),
                                'path' => $imageFilePath,
                                'fullPath' => $file->getPathname(),
                                'mime' => $mimeType
                            ];
                        }


                        if (in_array(strtolower($file->getExtension()), ['zip'])) 
                        {
                            $foundFiles[] = [
                                'type' => 'archive',
                                'name' => $file->getFilename(),
                                'path' => $imageFilePath,
                                'fullPath' => $file->getPathname(),
                                'mime' => $mimeType
                            ];
                        }
                    }

                }
            }


            
        }

        
        // return $res->send(url_to('FileManagerController@index'));

        // $arr = [
        //     'items' => $items, // Loaded files
        //     'currentUrl' => $this->currentUrl, // URL to the storage where files are stored and in which folder it is right now
        //     'baseUrl' => $baseUrl, // URL to the storage where files are stored
        //     'legacyUrl' => $legacyUrl, // URL to file manager route - https://sitename/filemanager
        //     'backUrl' => $backUrl // This url is for back button
        // ];


        return $res->render($this->configurations['viewPath'], [
            'items' => $foundFiles,
            'currentUrl' => $this->currentUrl,
            'baseUrl' => $baseUrl,
            'legacyUrl' => url_to('FileManagerController@index'),
            'backUrl' => url_to('FileManagerController@index'),
            'deep_search' => TRUE
        ]);


    }

}