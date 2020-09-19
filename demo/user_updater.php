<?php

declare(strict_types = 1);

/** @psalm-immutable */
class User
{

    /**
     * @var int The id of the user
     * @psalm-taint-source id
     */
    public int $id;

    /**
     * @var string The passport number of the user
     * @psalm-taint-source personal_data_secret
     */
    private string $passport;

    /**
     *
     * @param int $id
     * @param int|string $passport
     */
    public function __construct(
        int $id,
        $passport
    ) {
        $this->id = $id;
        $this->passport = $passport;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPassport(): int
    {
        return $this->passport;
    }

    public function getInfo(): array
    {
        return [
            'id' => $this->id,
            'passport' => $this->passport
        ];
    }

}

//class UserUpdater
//{
//    public static function deleteUser(
//        PDO $pdo,
//        User $user
//    ): void {
//        $pdo->exec("delete from users where user_id = " . $user->id);
//    }
//}

$userObj = new User(123, '123456789');

echo json_encode($userObj->getInfo());

//// remove the next line to fix issue
//UserUpdater::deleteUser(new PDO($dsn = ''), $userObj);