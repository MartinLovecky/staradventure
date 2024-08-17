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
        public Fluent $fluent,
        public Mailer $mailer
    ) {
    }

    /**
     * Retrieves member information
     * -  IF $column is provided , $value must be also provided -> same if not provided
     *  - IF you want just specific $item you must also provide $column and $value
     *
     * @param  ?string $column to search in the database. If null, fetch all members.
     * @param  ?string $value  to match in the specified column. If null, fetch all members.
     * @param  ?string $item   The specific item to fetch. If null, return array.
     * @return mixed
     */
    public function getMemberInfo(
        ?string $column = null,
        ?string $value = null,
        ?string $item = null
    ): mixed {
        $stmt = $this->fluent?->query
            ?->from('members')
            ?->leftJoin('info ON members.member_id = info.member')
            ?->select('info.*')
            ?->where($column, $value);
        if ($column && $value && $item) {
            // specific value
            return $stmt?->fetch($item);
        } elseif ($column && $value) {
            // array data for specific member
            return $stmt?->fetch();
        }
        // all members data
        return $stmt?->fetchAll();
    }

    /**
     * Inserts a new record into the specified table.
     *
     * @param  string $table  The name of the table to insert into.
     * @param  array  $values An associative array of column-value pairs to insert.
     * @return void
     */
    public function insert(string $table, array $values): void
    {
        $this->fluent?->query?->insertInto($table)?->values($values)?->execute();
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

    /**
     * Deletes a member from the members table by member ID.
     *
     * @param  string $memberID The ID of the member to delete.
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
     * Updates member information in the info table.
     *
     * @param  Member $member The Member entity containing updated information.
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
     * Updates the members table with specified fields.
     *
     * @param  array   $set      An associative array of fields and their new values.
     * @param  ?string $memberID The ID of the member to update. If null, it must be specified in the $set array.
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
