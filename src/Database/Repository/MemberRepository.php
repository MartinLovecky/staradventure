<?php

declare(strict_types=1);

namespace Mlkali\Sa\Database\Repository;

use Dotenv\Parser\Value;
use Mlkali\Sa\Http\Mailer;
use Mlkali\Sa\Database\Fluent;
use Mlkali\Sa\Support\Messages;
use Mlkali\Sa\Database\Entity\Member;

class MemberRepository
{
    public function __construct(
        public Messages $messages,
        public Fluent $fluent,
        public Mailer $mailer
    ) {
    }

    public function getMemberInfo(
        ?string $column = null,
        ?string $value = null,
        ?string $item = null
    ): mixed {
        $stmt = $this->fluent->query
            ->from('members')
            ->leftJoin('info ON members.member_id = info.member')
            ->select('info.*')
            ->where($column, $value);
        if ($column && $value && $item) {
            // specific value
            return $stmt->fetch($item);
        } elseif ($column && $value) {
            // array data for specific member
            return $stmt->fetch();
        }
        // all members data
        return $stmt->fetchAll();
    }

    /**
     * Inserts a new record into the specified table.
     *
     * @param  string $table  The name of the table to insert into.
     * @param  array  $values An associative array to insert.
     * @return void
     */
    public function insert(string $table, array $values): void
    {
        $this->fluent->query
            ->insertInto($table)
            ->values($values)
            ->execute();
    }

    /**
     * Sends an email using the Mailer class.
     *
     * @param  string $body    The body of the email.
     * @param  string $subject The subject of the email.
     * @param  string $to      The recipient email address.
     * @return void
     */
    public function sendEmail(
        string $body,
        string $subject,
        string $to
    ): void {
        $this->mailer->sender($body, $subject, $to);
    }

    public function deleteMember(string $id = ''): void
    {
        $this->fluent->query
            ->deleteFrom('members')
            ->where('member_id', $id)
            ->execute();
    }

    public function updateMembersTable(array $member): void
    {
        $this->fluent->query
            ->update('members')
            ->set($member)
            ->where('member_id', $member['member_id'])
            ->execute();
    }

    public function updateInfoTable(Member $member): void
    {
        $this->fluent->query
            ->update('info')
            ->set([
                "member_name" => $member->member_name,
                "member_surname" => $member->member_surname,
                "visible" => $member->visible,
                "location" => $member->location,
                "age" => $member->age,
                "member" => $member->member_id
            ])
            ->where('member', $member->member_id)
            ->execute();
    }

    public function exist(string $id = ''): bool
    {
        return $this->getMemberInfo(
            column:'member_id',
            value:$id,
            item:'member_id'
        ) == ! false;
    }
}
