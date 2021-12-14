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

    public function updateProduct($data)
    {
        $product = $this->hasProductExits($data['id']);

        if ($product) {
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
        } else {

            $this->errors['message'] = "Invalid Product Id";
            $this->errors['code'] = 404;
            return false;
        }
    }


    public function deleteProduct($data)
    {
        $product = $this->hasProductExits($data['id']);

        if ($product) {

            return $product->delete();
        } else {

            $this->errors['message'] = "Invalid Product Id";
            $this->errors['code'] = 404;
            return false;
        }
    }



    public function hasProductExits($productId)
    {
        return Product::find($productId);
    }
}
