<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AssetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array|\Illuminate\Contracts\Support\Arrayable|\JsonSerializable
     */
    public function toArray($request)
    {
        return  [
            'id' => (string)$this->id,
            'label' => (string)$this->label,
            'content' => (string)$this->content,
            'tag' => (string)$this->tag->id,
            'team' => (string)$this->team->id,
            'user' => (string)$this->user->id,
        ];
    }
}
