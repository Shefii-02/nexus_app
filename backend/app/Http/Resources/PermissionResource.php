<?php
namespace App\Http\Resources;
use Illuminate\Http\Resources\Json\JsonResource;

class PermissionResource extends JsonResource
{
    public function toArray($req)
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'label' => str_replace('.', ' ', ucfirst($this->name)),
        ];
    }
}
