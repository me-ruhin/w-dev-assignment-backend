<?php

namespace App\Models;

use App\Http\Resources\ProductResource;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Product extends Model
{
    use HasFactory;

    public $errors = [];
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['name', 'description', 'price', 'qty', 'image'];

    /**
     * it returns the list of product
     * @return Object
     */
    public function getProducts()
    {
        return ProductResource::collection(Product::get());
    }

    /**
     * it will add a new product record  in the Database
     * @param array
     * @return boolean
     */

    public function storeProduct($data)
    {
        try {
            DB::beginTransaction();
            Product::create($data);
            DB::commit();
            return true;
        } catch (\Exception $e) {

            DB::rollBack();
            $this->errors['message'] = $e->getMessage();
            $this->errors['code'] = $e->getCode();
            return false;
        }
    }

    /**
     * it will modify the product
     * @param array
     * @return boolean
     */

    public function updateProduct($data)
    {
        $product = $this->hasProductExits($data['id']);

        if ($product) {
            try {
                DB::beginTransaction();
                $product->update($data);
                DB::commit();
                return true;
            } catch (\Exception $e) {

                DB::rollBack();
                $this->errors['message'] = $e->getMessage();
                $this->errors['code'] = $e->getCode();
                return false;
            }
        } else {

            $this->errors['message'] = "Invalid Product Id";
            $this->errors['code'] = 404;
            return false;
        }
    }

    /**
     * it will remove the product record from Database
     * @param id
     * @return boolean
     */

    public function deleteProduct($productId)
    {
        $product = $this->hasProductExits($productId);

        if ($product) {
            return $product->delete();
        } else {

            $this->errors['message'] = "Invalid Product Id";
            $this->errors['code'] = 404;
            return false;
        }
    }

    /**
     *local scope for product model
     * @param keyword
     * @return statement
     */

    public function scopeSearchByKeyword($query, $keyword)
    {
        return $query->where('name', 'like', '%' . $keyword . '%');
    }

    /**
     *it will check the availablity of record in the Database  with belonging keyword
     * @param keyword
     * @return Illuminate\Support\Facades\Response\JSON
     */

    public function searchByKeword($keyword)
    {

        return  ProductResource::collection(Product::searchByKeyword($keyword)->get());
    }

    /**
     * it will remove the product Image from public path
     * @param id
     * @return boolean
     */

    public function removeFile($productId)
    {
        $result = $this->hasProductExits($productId);

        if (isset($result->image)) {
            unlink(base_path() . '/public/storage/product/' . $result->image);
        }
    }

     /**
     * it will check the availablity of record in the Database
     * @param id
     * @return boolean
     */


    public function hasProductExits($productId)
    {
        return Product::find($productId);
    }

     /**
     * it will return the specefic product price
     * @param id
     * @return boolean
     */


    public function getProductById($productId){
        return Product::select('price')->find($productId);
    }
}
