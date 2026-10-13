<?php

namespace Modules\Virtualcard\Entities;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class VirtualcardCategory extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'status'];
}
