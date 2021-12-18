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
     *
     * @return Illuminate\Support\Facades\Response\Json     *
     */

    public function index()
    {

        return $this->product->getProducts();
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return Illuminate\Support\Facades\Response\Json
     */
    public function store(ProductRequest $request)
    {

        if ($request->hasFile('photo')) {
            $imageName =  uploadImage('product/', $request->file('photo'));
            $request->merge(['image' => $imageName]);
        }
        $result = $this->product->storeProduct($request->all());

        if (!$result) {
            return $this->sendError($this->product->errors, $this->product->errors['message'], $this->product->errors['code']);
        }
        return $this->sendResponse($this->index(), "Product successfully added", 200);
    }

    /**
     * Display the specified resource from Database.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($keyword)
    {

        $result = $this->product->searchByKeword($keyword);

        if (count($result) <= 0) {
            return $this->sendError('', 'product not found', 404);
        }
        return $this->sendResponse($result, "Product found", 200);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * It will update  the specefic Product from storage
     * @param name,description,price,qty,image
     * @return Illuminate\Support\Facades\Response\Json
     */

    public function update(ProductUpdateRequest $request, $id)
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
        return $this->sendResponse($this->index(), "Product successfully update", 200);
    }

    /**
     * It will remove the  Product from storate
     *
     * @param productId int
     *
     * @return Illuminate\Support\Facades\Response\Json
     */

    public function destroy($productId)
    {

        $result = $this->product->deleteProduct($productId);

        if (!$result) {
            return $this->sendError($this->product->errors, $this->product->errors['message'], $this->product->errors['code']);
        }
        return $this->sendResponse($this->index(), "Product successfully deleted", 200);
    }
}
