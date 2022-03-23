<?php

namespace App\Http\Resources\User;
use App\Models\User;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Helpers\Helper;
class UserResource extends JsonResource
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
            'first_name_kana' => $this->first_name_kana,
            'last_name_kana' => $this->last_name_kana,
            'gender' => $this->gender,
            'birthday' => $this->birthday,
            'nick_name' => $this->nick_name,
            'avatar' => User::getFileUrl($this->avatar),
            'position_id' => $this->position_id,
            'description' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'notify' => $this->notify,
            'role_identifier' => $this->role->role_identifier,
            'role_name' => $this->role->role_name,
            'role_id' => $this->role->id,
            'udp_ver' => $this->udp_ver
        ];
    }
}
