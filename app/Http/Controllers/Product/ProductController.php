<?php

namespace App\Http\Controllers\Product;

require '../vendor/autoload.php';

use Aws\S3\S3Client;
use Aws\Exception\AwsException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Model\Product;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function store(Request $request)
    {
        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-2', //Region of the bucket
            'credentials' => array(
                'key' => 'AKIA4NZLDOQGJT6PZ77C',
                'secret'  => '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',
            )
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
        $resume = $request->file('image');

        // $request->file('image')->move(base_path() . '/storage/app/public/', $resume);
        //Get a command to GetObject
        $cmd = $s3Client->getCommand('GetObject', [
            'Bucket' => 'teacity-storage-image',
            'Key'    => $resume
        ]);

        //The period of availability
        $request = $s3Client->createPresignedRequest($cmd, '+10 minutes');

        //Get the pre-signed URL
        $signedUrl = (string) $request->getUri();

        $product = new Product($request->all());
        $product->image = $resume;
        $product->save();
        return  response()->json($product);
    }
    public function index()
    {
        $s3Client = new S3Client([
            'version'     => 'latest',
            'region'      => 'us-east-2', //Region of the bucket
            'credentials' => array(
                'key' => 'AKIA4NZLDOQGJT6PZ77C',
                'secret'  => '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',
            )
        ]);



        $listProduct =  Product::all();
        foreach ($listProduct as $key) {
            // $key['image'] = env('APP_URL') . '/storage/app/public/' . $key['image'];
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
        unlink(storage_path('app/public/' . $product->image));
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
