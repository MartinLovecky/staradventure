<?php

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Database\Repository\MemberRepository;
use Mlkali\Sa\Support\Encryption;

class Member
{
    public function __construct(
        private MemberRepository $memberRepository,
        public array $data = []
    ) {
        $this->getMember();
    }

    public function __get(string $name): mixed
    {
        return $this->data[$name] ?? null;
    }

    public function __set(string $name, $value): void
    {
        $this->data[$name] = $value;
    }

    /**
     * Loads the member data from session or cookie.
     *
     * If a member is found in the session, it is unserialized and its data is set.
     * If the session does not contain member data but a 'remember' cookie exists,
     * the user is fetched based on the cookie token.
     * If neither is available, the member is marked as not logged in.
     *
     * @return void
     */
    private function getMember(): void
    {
        if (isset($_SESSION['member'])) {
            ;
            $this->logged = true;
            $member = unserialize($_SESSION['member']);
            foreach ($member as $key => $value) {
                $this->{$key} = $value;
            }
        } elseif (!isset($_SESSION['member']) && isset($_COOKIE['remember'])) {
            $this->getUserFromCookieToken();
        } else {
            $this->logged = false;
        }
    }

    /**
     * Retrieves user information from the cookie token and updates session data.
     *
     * The method decrypts the cookie token to get the username and IP address.
     * If the IP address matches the current remote address, it fetches member data
     * from the repository and stores it in the session.
     *
     * @return void
     */
    private function getUserFromCookieToken(): void
    {
        $encryption = new Encryption();
        $parts = explode('|', $_COOKIE['remember']);
        $username = $encryption->decrypt($parts[0]) ?? null;
        $id = $encryption->decrypt($parts[1]) ?? null;

        if ($id === $_SERVER['REMOTE_ADDR']) {
            $memberData = $this->memberRepository->getMemberInfo('username', $username);
            $memberData['logged'] = true;
            $_SESSION['member'] = serialize($memberData);
        }
    }
}
