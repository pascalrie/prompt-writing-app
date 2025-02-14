<?php

namespace App\Enum;

class MessageOfResponse
{
    public const SUCCESS = ' was successful.';
    public const NOT_SUCCESS = ' was not successful.';
    public const NOT_FOUND = ' was not found.';
    public const USE_EXISTING = ' Please use an existing entity.';

    public const NO_BODY_PARAMETERS = 'Body Parameters of Request are missing. 
                                        Please provide all of the necessary parameters in a json-encoded format. ';
}
