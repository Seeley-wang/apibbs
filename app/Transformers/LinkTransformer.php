<?php
/**
 * Created by PhpStorm.
 * User: Clown
 * Date: 18/7/18
 * Time: 下午3:13
 */

namespace App\Transformers;


use App\Models\Link;
use League\Fractal\TransformerAbstract;

class LinkTransformer extends TransformerAbstract
{
    public function transform(Link $link)
    {
        return [
          'id' => $link->id,
          'title' => $link->title,
          'link' => $link->link
        ];
    }
}