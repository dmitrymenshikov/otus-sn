<?php

declare(strict_types=1);

namespace App\User\Model\Form\DTO;

use App\User\Model\Enum\GenderEnum;
use DateTime;

class UserRegisterTypeDTO
{
    public string $email;
    public string $password;
    public string $firstname;
    public string $lastname;
    public DateTime $birthday;
    public GenderEnum $gender;
    public string $city;
    public string $about;
}