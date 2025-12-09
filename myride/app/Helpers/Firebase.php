<?php
namespace App\Helpers;
use App\Helpers\Generator;
use Kreait\Firebase\Factory;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\ServiceAccount;
use DateTime;

class Firebase
{
    private static $factory;
    private static $database;

    public static function init()
    {
        if (!self::$factory) {
            self::$factory = (new Factory)
                ->withServiceAccount(base_path('/firebase/myride-a0077-firebase-adminsdk-7x7j4-6b7da5321a.json'))
                ->withDatabaseUri('https://myride-a0077-default-rtdb.firebaseio.com/');
        }
        if (!self::$database) {
            self::$database = self::$factory->createDatabase();
        }
    }

    public static function uploadFile($ctx, $user_id, $username, $file, $file_ext){
        self::init();
        // Firebase Storage instance
        $storage = self::$factory->createStorage();
        $bucket = $storage->getBucket();
        $uploadedFile = fopen($file->getRealPath(), 'r');
        $id = Generator::getUUID();

        // Upload file to Firebase Storage
        $object = $bucket->upload($uploadedFile, [
            'name' => $ctx.'/' . $user_id . '_' . $username . '/' . $id . '.' . $file_ext,
            'predefinedAcl' => 'publicRead',
            // 'contentType' => $file_ext,
            'metadata' => [
                'contentType' => $file->getMimeType(),       
                'contentDisposition' => 'inline',            
            ]
        ]);

        // Uploaded link
        $object->update(['acl' => [],]);                
        $fileUrl = $object->signedUrl(
            new \DateTime('+10 year'),
            [
                'version' => 'v2',
                'responseDisposition' => 'inline'
            ]
        );

        return $fileUrl;
    }

    public static function deleteFile($url){
        self::init();
        $storage = self::$factory->createStorage();
        $bucket = $storage->getBucket();

        $parsedUrl = parse_url($url);
        if (!isset($parsedUrl['path'])) {
            return false; 
        }

        $path = urldecode(substr($parsedUrl['path'], strpos($parsedUrl['path'], '/o/') + 3));
        $object = $bucket->object($path);

        if ($object->exists()) {
            $object->delete();
            return true; 
        }

        return false; 
    }

    public function insert_command($path, $data)
    {
        self::init(); 
        $data['created_at'] = date('Y-m-d H:i:s');
        $reference = self::$database->getReference($path);
        $reference->set($data);
    }
}