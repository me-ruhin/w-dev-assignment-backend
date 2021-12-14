<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderMaster extends Model
{
    use HasFactory;

    const PENDING = 0;
    const REJECTED = 1;
    const APPROVED = 2;
    const PROCESSING = 3;
    const SHIPPED = 4;
    const DELIVERED = 5;
}
