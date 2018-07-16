<?php
/**
 * Created by PhpStorm.
 * User: Clown
 * Date: 7/16/18
 * Time: 9:47 PM
 */

namespace App\Transformers;


use App\Models\Category;
use League\Fractal\TransformerAbstract;

class CategoryTransformer extends TransformerAbstract
{
    public function transform(Category $category)
    {
        return [
            'id' => $category->id,
            'name' => $category->name,
            'description' => $category->description,
        ];
    }
}