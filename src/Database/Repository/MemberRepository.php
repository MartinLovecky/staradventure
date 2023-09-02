<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\DB;
use Mlkali\Sa\Support\Mailer;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Support\Encryption;
use Mlkali\Sa\Database\Entity\Member;

class MemberRepository
{
    public function __construct(
        private DB $db,
        private Mailer $mailer,
        private Encryption $enc,
        private Messages $message
    ) {
    }

    public function getMemberInfo(?string $column = null, ?string $value = null, ?string $item = null): mixed
    {
        $stmt = $this->db?->query
            ?->from('members')
            ?->leftJoin('info ON members.member_id = info.member')
            ?->select('info.*')
            ?->where($column, $value);
        if (!$column && !$value) {
            return $stmt?->fetchAll();
        }
        return $stmt?->fetch($item);
    }

    public function insert(string $table, array $values): void
    {
        $this->db?->query?->insertInto($table)?->values($values)?->execute();
    }

    public function sendEmail(array $data): void
    {
        $dynamic = $this->message->createEmailMessage(
            $data['templateType'],
            [
                $data['username'],
                $_SERVER['SERVER_NAME'],
                rand(),
                $data['encryptedID'],
                $data['active']
            ]
        );

        $body = str_replace('TEMPLATE', $dynamic, $this->message->main());

        $info = Messages::getEmailInfo($data['templateType'], $data['recipient']);

        $this->mailer->sender($body, $info);
    }

    public function deleteMember(string $memberID): void
    {
        $this->db?->query
            ?->deleteFrom('members')
            ?->where('member_id', $memberID)
            ?->execute();
    }

    public function updateInfoMember(Member $member): void
    {
        $set = [
            'member_name' => $member?->name,
            'member_surname' => $member?->surname,
            'visible' => $member?->visible,
            'location' => $member?->location,
            'age' => $member?->age
        ];

        $this->db?->query
            ?->update('info')
            ?->set($set)
            ?->where('member', $member?->memberID)
            ?->execute();
    }

    /**
     * Update members table inside db
     * - I am lazzy so I use null safe operator -> latter logs -> error reports
     * @param array $set 'fileds' you want update ['filed_name' => $value]
     * @param string|null $memberID
     * @return void
     */
    public function update(array $set, ?string $memberID)
    {
        $this->db?->query
            ?->update('members')
            ?->set($set)
            ?->where('member_id', $memberID)
            ?->execute();
    }
}
