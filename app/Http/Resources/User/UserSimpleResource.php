<?php

namespace App\Http\Resources\User;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;

class UserSimpleResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'username' => $this->first_name." ".$this->last_name,
            'email' => $this->email,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'avatar' => User::getFileUrl($this->avatar),
            'role_identifier' => $this->role->role_identifier,
        ];
    }
}
