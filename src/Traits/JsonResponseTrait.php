<?php

namespace App\Traits;

trait JsonResponseTrait
{
    private string $successMessage = 'Success';
    private string $errorMessage = 'Error';
    private string $warningMessage = 'Warning';
    private string $successCode = '200';
    private string $notFoundCode = '404';
    private string $notFoundMessage = 'Not Found';
    private string $serverErrorCode = '500';
    private string $serverErrorMessage = 'Internal Server Error';

    public function appendTimeStampToApiResponse(array $response): array
    {
        $dateAndTime = new \DateTime('NOW');
        $response['time_stamp'] = $dateAndTime;
        return $response;
    }

    public function appendMessageToSuccessfulApiResponse(array $response): array
    {
        $response += ['code' => $this->successCode, 'message' => $this->successMessage];
        return $response;
    }


}