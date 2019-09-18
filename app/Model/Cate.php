<?php


namespace App\Model;


use Encore\Admin\Traits\AdminBuilder;
use Encore\Admin\Traits\ModelTree;
use Illuminate\Database\Eloquent\Model;

class Cate extends Model
{
    use ModelTree, AdminBuilder;

    protected $table = 'cate';

    //public $timestamps = false;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        $this->setParentColumn('pid');
    }
}