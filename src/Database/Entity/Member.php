<?php

namespace Mlkali\Sa\Database\Entity;

use Mlkali\Sa\Support\Mailer;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Database\Repository\MemberRepository;

class Member extends MemberRepository
{

    public function __construct(
        private Mailer $mailer,
        private Encryption $enc,
        private bool $logged = false,
        private bool $visible = false,
        private string $username = 'visitor',
        private ?string $memberEmail = null,
        private ?string $activeMember = null,
        private string $permission = 'visit',
        private ?string $memberName = null,
        private ?string $memberSurname = null,
        private string $avatar = 'empty_profile.png',
        private ?string $resetToken = null,
        private bool $resetComplete = false,
        private int $bookmarkCount = 0,
        private array $bookmarks = [],
        private ?string $memberID = null
    )
    {
    }

    public function __set($name, $value)
    {
        if (property_exists($this, $name)) {
            $this->$name = $value;
        }
    }

    public function __get($name)
    {
        if (property_exists($this, $name)) {
            $this->name = $this->getMemberInfo($this->memberID, $name);
            return $this->$name;
        }
    }

    public function getMemberInfo(string $memberID, $item = null): bool|array
    {
        $stmt = $this->db->query
            ->from('members')
            ->leftJoin('info ON members.member_id = info.member')
            ->select('info.*')
            ->where('member', $memberID);

        return $stmt->fetch($item);
    }

    public function registerMember($request)
    {
        $this->insertIntoInfo($request->username . '|' . $request->email);
        $data = $this->insertIntoMember($request);

        $memberEncryptedID = $this->enc->encrypt($request->username . '|' . $request->email);

        $info = ['subject' => 'Potvrzení registrace', 'to' => $request->email];
        $body = str_replace(
            ['YourUsername', 'MemberID', 'ACHASH', 'TOKEN', 'URL'],
            [$request->username, $data['id'], $memberEncryptedID, $data['token'], $_SERVER['HTTP_ORIGIN']],
            file_get_contents($_SERVER['DOCUMENT_ROOT'] . '/public/template/register.php')
        );

        $this->mailer->sender($body, $info);
    }

    public function activateMember($memberID, $token)
    {
        $decryptID = $this->enc->decrypt($memberID);

        $memberToken = $this->getMemberInfo($decryptID, 'active');
        $memberTID = $this->getMemberInfo($decryptID, 'member_id');

        if (
            strcmp($token, $memberToken) === 0 &&
            strcmp($decryptID, $memberTID) === 0
        ) {
            $this->updateMember($decryptID, ['active' => 'yes']);
        }
    }
}
