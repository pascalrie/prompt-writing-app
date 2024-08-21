<?php

namespace App\Traits;

trait JsonAppendTrait
{
    public function appendTimeStampToApiResponse(array $response): array
    {
        $dateAndTime = new \DateTime('NOW');
        $response['time_stamp'] = $dateAndTime;
        return $response;
    }
}