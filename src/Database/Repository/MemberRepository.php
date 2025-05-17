<?php

declare(strict_types=1);

namespace Mlkali\Sa\Database\Repository;

use LDAP\Result;
use Mlkali\Sa\Database\Fluent;
use Mlkali\Sa\Http\Request;
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
     * @param mixed $item
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

        if (!$column && !$value && count($item) === 0) {
            return $stmt->fetchAll();
        } elseif (!$column && !$value && count($item) === 1) {
            return array_keys($stmt->fetchAll(...$item));
        } elseif (!$column && !$value && count($item) === 2) {
            return $stmt->fetchPairs(...$item);
        } elseif (!$column && !$value && count($item) > 2) {
            return $stmt->fetchAll(...$item);
        } elseif ($column && $value && count($item) <= 1) {
            return $stmt->fetch(...$item);
        } elseif ($column && $value && count($item) === 2) {
            return $stmt->fetchPairs(...$item);
        } else {
            return $stmt->fetchAll(...$item);
        }
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

    public function updateMembersTable(array $set, string $memberID): void
    {
        $this->fluent->query
        ->update('members')
        ->set($set)
        ->where('member_id', $memberID)
        ->execute();
    }

    /* "member_name" => $member->member_name,
    "member_surname" => $member->member_surname,
    "visible" => $member->visible,
    "location" => $member->location,
    "age" => $member->age,
    "member" => $member->member_id
    */
    public function updateInfoTable(array $set, string $memberID): void
    {
        $this->fluent->query
        ->update('info')
        ->set($set)
        ->where('member', $memberID)
        ->execute();
    }
}
