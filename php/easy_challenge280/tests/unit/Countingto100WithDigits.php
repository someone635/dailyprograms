<?php

namespace C280\tests\unit;

use C280\src\CountingTo100WithDigits;

class Countingto100WithDigitsTest extends \PHPUnit_Framework_TestCase
{
    public function test_classExists()
    {
        $this->assertTrue(class_exists('C\Higgs\User\UserFileWriter'));
    }

    public function test_writeUser_savesUsername_toTheRegistry_ifNotExists()
    {

        $this->writeUserToRegistry(
            $username = 'valid@example.com',
            $password = '$2y$10$0heS/2BqL8V3kmE2FCr3kuS.XHVSBsuLo2YZLUt6xM8cTKmNKodxS',
            $email_confirmed = false,
            $email_confirmation_token = uniqid('etoken')
        );


        $users = json_decode(file_get_contents($this->abs_registry_path));
        $tested_user = $this->getUser($users, $username);

        $this->assertEquals($username, $tested_user->username);

    }



    public function test_writeUser_savesPasswordHash_toTheRegistry_ifUsernameNotExists()
    {

        $this->writeUserToRegistry(
            $username = 'valid@example.com',
            $password = '$2y$10$0heS/2BqL8V3kmE2FCr3kuS.XHVSBsuLo2YZLUt6xM8cTKmNKodxS',
            $email_confirmed = false,
            $email_confirmation_token = uniqid('etoken')
        );

        $users = json_decode(file_get_contents($this->abs_registry_path));
        $tested_user = $this->getUser($users, $username);

        $this->assertEquals($password, $tested_user->password);
    }




    public function test_writeUser_savesEmailConfirmed_toTheRegistry_ifUsernameNotExists()
    {
        $this->writeUserToRegistry(
            $username = 'valid@example.com',
            $password = '$2y$10$0heS/2BqL8V3kmE2FCr3kuS.XHVSBsuLo2YZLUt6xM8cTKmNKodxS',
            $email_confirmed = false,
            $email_confirmation_token = uniqid('etoken')
        );

        $users = json_decode(file_get_contents($this->abs_registry_path));
        $tested_user = $this->getUser($users, $username);

        $this->assertEquals($email_confirmed, $tested_user->email_confirmed);


    }

    public function test_writeUser_savesEmailConfirmationToken_toTheRegistry_ifUsernameNotExists()
    {
        $this->writeUserToRegistry(
        $username = 'valid@example.com',
        $password = '$2y$10$0heS/2BqL8V3kmE2FCr3kuS.XHVSBsuLo2YZLUt6xM8cTKmNKodxS',
        $email_confirmed = false,
        $email_confirmation_token = uniqid('etoken')
    );

    $users = json_decode(file_get_contents($this->abs_registry_path));
    $tested_user = $this->getUser($users, $username);

    $this->assertEquals($email_confirmation_token, $tested_user->email_confirmation_token);
}

    public function test_writeUser_throwsUsernameExistsException_ifEmailAlreadyExists()
    {
        $this->writeUserToRegistry(
            $username = 'valid@example.com',
            $password = '$2y$10$0heS/2BqL8V3kmE2FCr3kuS.XHVSBsuLo2YZLUt6xM8cTKmNKodxS',
            $email_confirmed = false,
            $email_confirmation_token = uniqid('etoken')
        );

        $this->expectException('Pillr\Higgs\User\UsernameExistsException');
        $this->writeUserToRegistry(
            $username = 'valid@example.com',
            $password = '$2y$10$0heS/2BqL8V3kmE2FCr3kuS.XHVSBsuLo2YZLUt6xM8cTKmNKodxS',
            $email_confirmed = false,
            $email_confirmation_token = uniqid('etoken')
        );
    }

    //Helpers
    public function writeUserToRegistry($username, $password, $email_confirmed, $email_confirmation_token)
    {

        $this->user->expects($this->any())
        ->method('getUsername')
        ->willReturn($username);

        $this->user->expects($this->any())
        ->method('getPassword')
        ->willReturn($password);

        $this->user->expects($this->any())
        ->method('isConfirmed')
        ->willReturn($email_confirmed);

        $this->user->expects($this->any())
        ->method('getConfirmationToken')
        ->willReturn($email_confirmation_token);

        $writer = new UserFileWriter();
        $writer->writeUser($this->user, $this->abs_registry_path);
    }

    public function getUser($users, $username)
    {
        $current_user = null;
        foreach ($users as $user) {
            if ($user->username === $username) {
                $current_user = $user;
            }
        }

        return $current_user;
    }


    public function tearDown()
    {
        if (file_exists($this->abs_registry_path)) {
            unlink($this->abs_registry_path);
            unset($this->user);
        }
    }

}
