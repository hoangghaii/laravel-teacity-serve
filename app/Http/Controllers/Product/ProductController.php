<?php

namespace App\Http\Controllers\Product;

require '../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Aws\S3\ObjectUploader;
use Aws\Credentials\Credentials;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $credentials = new Credentials('AKIA4NZLDOQGDVYJXUMV', 'wB9S8SjQFW+s6U8aYyMMvko9mOqgvoLgMRDU68QF');

        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-2', //Region of the bucket
            'credentials' => $credentials
        ]);

        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'name' => 'required|string',
            'price' => 'required',
            'size' => 'required|string',
            'image' => 'required',
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 404);
        }

        // $resume = time() . '.' .  $request->file('image')->getClientOriginalExtension();
        $resume = time() . '.' .  $request->file('image')->getClientOriginalExtension();


        // $resume = $request->file('image');
        // try {
        //     $s3Client->putObject([
        //         'Bucket' => 'teacity-storage-image',
        //         'Key' =>  $request->file('image'),
        //     ]);
        // } catch (S3Exception $e) {
        //     // Catch an S3 specific exception.
        //     echo $e->getMessage();
        // }

        // $product = new Product($request->all());
        // $product->image = $request->image;
        // $product->save();
        // return  response()->json($product);
        return $request->file('image');
    }

    public function index()
    {
        $credentials = new Credentials('AKIA4NZLDOQGDVYJXUMV', 'wB9S8SjQFW+s6U8aYyMMvko9mOqgvoLgMRDU68QF');

        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-2', //Region of the bucket
            'credentials' => $credentials
        ]);

        $listProduct =  Product::all();

        foreach ($listProduct as $key) {
            //Get a command to GetObject
            $cmd = $s3Client->getCommand('GetObject', [
                'Bucket' => 'teacity-storage-image',
                'Key'    => $key['image']
            ]);

            //The period of availability
            $request = $s3Client->createPresignedRequest($cmd, '+10 minutes');

            //Get the pre-signed URL
            $signedUrl = (string) $request->getUri();
            $key['image'] = $signedUrl;
        }
        return $listProduct;
    }

    public function destroy(Request $request)
    {
        $product =  Product::find($request->id);

        $credentials = new Credentials('AKIA4NZLDOQGDVYJXUMV', 'wB9S8SjQFW+s6U8aYyMMvko9mOqgvoLgMRDU68QF');

        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-2', //Region of the bucket
            'credentials' => $credentials
        ]);

        try {
            $s3Client->deleteObject([
                'Bucket' => 'teacity-storage-image',
                'Key' => $product->image,
            ]);
        } catch (S3Exception $e) {
            // Catch an S3 specific exception.
            echo $e->getMessage();
        }

        return response()->json($product->delete());
    }

    public function update(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'description' => 'required|string',
            'name' => 'required|string',
            'price' => 'required',
            'size' => 'required|string',
            'image' => 'required',
            'category_id' => 'required',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 404);
        }
        $product =  Product::find($request->id);
        unlink(storage_path('app/public/' . $product->image));

        $resume = time() . '.' .  $request->file('image')->getClientOriginalExtension();
        $request->file('image')->move(base_path() . '/storage/app/public/', $resume);

        $product->description = $request->description;
        $product->name = $request->name;
        $product->price = $request->price;
        $product->size = $request->size;
        $product->image = $resume;
        $product->category_id = $request->category_id;
        return response()->json($product->save());
    }
}
