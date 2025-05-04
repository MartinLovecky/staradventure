<?php

declare(strict_types=1);

namespace Mlkali\Sa\Database\Repository;

use Mlkali\Sa\Database\Fluent;
use Mlkali\Sa\Support\Messages;

class MemberRepository
{
    public function __construct(
        public Messages $messages,
        public Fluent $fluent
    ) {
    }

    /**
     *
     * @param string|null $column
     * @param string|null $value
     * @param array $item
     * @return mixed
     */
    public function getMemberInfo(
        ?string $column = null,
        ?string $value = null,
        array $item = []
    ): mixed {
        $stmt = $this->fluent->query
            ->from('members')
            ->leftJoin('info ON members.member_id = info.member')
            ->select('info.*')
            ->where($column, $value);

        return count($item) === 1
            ? $stmt->fetch(...$item)
            : $stmt->fetchAll(...$item);
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

    public function delete(string $id = ''): void
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
    /* "member_name" => $member->member_name,
        "member_surname" => $member->member_surname,
        "visible" => $member->visible,
        "location" => $member->location,
        "age" => $member->age,
        "member" => $member->member_id
    */
    public function updateInfoTable(array $member): void
    {
        $this->fluent->query
            ->update('info')
            ->set($member)
            ->where('member', $member['member_id'])
            ->execute();
    }

    public function exist(string $id = ''): bool
    {
        return $this->getMemberInfo(
            column:'member_id',
            value:$id,
            item:['member_id']
        ) == ! false;
    }
}
