<?php

namespace App\Http\Controllers;


use App\Models\Product;
use Illuminate\Http\Request;
use App\Http\Requests\ProductRequest;
use App\Http\Requests\ProductUpdateRequest;

class ProductController extends BaseController
{

    public $product;

    /**
     * it resolves the dependancy of product class
     * @return object
     */
    public function __construct(Product $productObj)
    {
        $this->product = $productObj;
    }
    /**
     * It will return the Product lists
     * @return Illuminate\Support\Facades\Response\Json
     */
    public function getProducts()
    {
        return $this->product->getProducts();
    }

    /**
     * It will store new  Product
     * @param name,description,price,qty,image
     * @return Illuminate\Support\Facades\Response\Json
     */

    public function storeProduct(ProductRequest $request)
    {

        if ($request->hasFile('photo')) {
            $imageName =  uploadImage('product/', $request->file('photo'));
            $request->merge(['image' => $imageName]);
        }
        $result = $this->product->storeProduct($request->all());

        if (!$result) {
            return $this->sendError($this->product->errors, $this->product->errors['message'], $this->product->errors['code']);
        }
        return $this->sendResponse($this->getProducts(), "Product successfully added", 200);
    }


    /**
     * It will update  Product
     * @param name,description,price,qty,image
     * @return Illuminate\Support\Facades\Response\Json
     */

    public function updateProduct(ProductUpdateRequest $request)
    {
        if ($request->hasFile('photo')) {
            $this->product->removeFile($request->id);
            $imageName =  uploadImage('product/', $request->file('photo'));
            $request->merge(['image' => $imageName]);
        }
        $result = $this->product->updateProduct($request->all());

        if (!$result) {
            return $this->sendError($this->product->errors, $this->product->errors['message'], $this->product->errors['code']);
        }
        return $this->sendResponse($this->getProducts(), "Product successfully update", 200);
    }



    /**
     * It will remove the  Product from storate 
     * @param productId
     * @return Illuminate\Support\Facades\Response\Json
     */

    public function deleteProduct($productId)
    {
        $result = $this->product->deleteProduct($productId);

        if (!$result) {
            return $this->sendError($this->product->errors, $this->product->errors['message'], $this->product->errors['code']);
        }
        return $this->sendResponse($this->getProducts(), "Product successfully deleted", 200);
    }


    public function searchByKeword($keyword)
    {
        $result = $this->product->searchByKeword($keyword);

        if (count($result) <= 0) {
            return $this->sendError('', 'product not found', 404);
        }
        return $this->sendResponse($result, "Product found", 200);
    }
}
