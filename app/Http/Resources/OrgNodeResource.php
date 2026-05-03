<?php
// app/Http/Resources/OrgNodeResource.php
namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class OrgNodeResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id'       => $this->id,
            'title'    => mb_strtoupper($this->name),            // “Jabatan” → uppercase untuk header abu-abu
            'name'     => $this->whenLoaded('member', fn() => $this->member?->name),
            'photo'    => $this->whenLoaded('member', fn() => $this->member?->photo_url), // sesuaikan field foto di tabel member
            'order'    => $this->order,
            'children' => OrgNodeResource::collection($this->whenLoaded('childrenRecursive')),
            // kolom opsional buat FE:
            // 'subtitle' => $this->member?->position_label,
            // 'meta'     => ['email' => $this->member?->email],
        ];
    }
}
