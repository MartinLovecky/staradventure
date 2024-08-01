<?php

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\Fluent;
use Mlkali\Sa\Support\Mailer;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Database\Entity\Member;

class MemberRepository
{
    public function __construct(
        public Messages $messages,
        private Fluent $fluent,
        private Mailer $mailer
    ) {
    }

    /**
     * Method getMemberInfo
     *
     * @param ?string $column [explicite description]
     * @param ?string $value [explicite description]
     * @param ?string $item [explicite description]
     *
     * @return mixed
     */
    public function getMemberInfo(?string $column = null, ?string $value = null, ?string $item = null): mixed
    {
        $stmt = $this->fluent?->query
            ?->from('members')
            ?->leftJoin('info ON members.member_id = info.member')
            ?->select('info.*')
            ?->where($column, $value);
        if (!$column && !$value) {
            return $stmt?->fetchAll();
        }
        return $stmt?->fetch($item);
    }

    /**
     * Method insert
     *
     * @param string $table [explicite description]
     * @param array $values [explicite description]
     *
     * @return void
     */
    public function insert(string $table, array $values): void
    {
        $this->fluent?->query?->insertInto($table)?->values($values)?->execute();
    }

    /**
     * Method sendEmail
     *
     * @param array $data [explicite description]
     *
     * @return void
     */
    public function sendEmail(array $data): void
    {
        $dynamic = $this->messages->createEmailMessage(
            $data['templateType'],
            [
                $data['username'],
                $_SERVER['SERVER_NAME'],
                rand(),
                $data['encryptedID'],
                $data['active']
            ]
        );

        $body = str_replace('TEMPLATE', $dynamic, $this->messages->main());

        $info = Messages::getEmailInfo($data['templateType'], $data['recipient']);

        $this->mailer->sender($body, $info);
    }

    /**
     * Method deleteMember
     *
     * @param string $memberID [explicite description]
     *
     * @return void
     */
    public function deleteMember(string $memberID): void
    {
        $this->fluent?->query
            ?->deleteFrom('members')
            ?->where('member_id', $memberID)
            ?->execute();
    }

    /**
     * Method updateInfoMember
     *
     * @param Member $member [explicite description]
     *
     * @return void
     */
    public function updateInfoMember(Member $member): void
    {
        $set = [
            'member_name' => $member?->name,
            'member_surname' => $member?->surname,
            'visible' => $member?->visible,
            'location' => $member?->location,
            'age' => $member?->age
        ];

        $this->fluent?->query
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
    public function update(array $set, ?string $memberID): void
    {
        $this->fluent?->query
            ?->update('members')
            ?->set($set)
            ?->where('member_id', $memberID)
            ?->execute();
    }
}
