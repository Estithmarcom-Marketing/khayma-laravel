<?php

namespace App\Services\V1\Website\CustomTent;

use App\Enums\CustomTent\CustomTentStatus;
use App\Events\CustomTent\CustomTentPlaced ;
use App\Models\CustomTent;
use Illuminate\Support\Facades\DB;

class CustomTentService
{
    public function store(array $data){
        $user = auth('sanctum')->user();
        $userId = $user?->id;
       $result = DB::transaction(function () use ($data, $userId){
                $customTenant = CustomTent::create([
                    'user_id'=> $userId ,
                    'user_name'=> $data['user_name'],
                    'phone' => $data['phone'],
                    'description'=> $data['description'],
                    'size' => $data['size'] ?? null, 
                    'status' => CustomTentStatus::PENDING  
                    ]);

                if (isset($data['images']) && is_array($data['images'])) {
                    foreach ($data['images'] as $image) {
                        $customTenant->addMedia($image)->toMediaCollection('customTent');
                    }
                }
                    return $customTenant->load(['media','user']);
        });
         event(new CustomTentPlaced($result));
       return $result;
    }

}
