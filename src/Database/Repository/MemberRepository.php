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
        private Encryption $enc
    ) {
    }

    /**
     * This function gets data from specific member or all members data
     *
     * @param string|null $column name form DB example: 'member_id' etc..
     * @param string|null $value of column we search
     * @param string|null $item column value to be recived
     * @return mixed 
     * - if $column && $value is null where() is ignored
     * - return fetchAll()
     * - if $item is string fetch() retrun string
     * - if $item is null fetch() return array
     */
    public function getMemberInfo(?string $column = null, ?string $value = null, ?string $item = null)
    {
        $stmt = $this->db->query
            ->from('members')
            ->leftJoin('info ON members.member_id = info.member')
            ->select('info.*')
            ->where($column, $value);
        if (!$column && !$value) {
            return $stmt->fetchAll();
        }
        return $stmt->fetch($item);
    }
    /**
     * Inserts values into the specified table
     *
     * @param string $table The table to insert values into
     * @param array $values The values to insert
     * @return void
     */
    public function insert(string $table, array $values): void
    {
        $this->db->query->insertInto($table)->values($values)->execute();
    }
    /**
     * Activates the member with the specified ID
     *
     * @param string $memberID The ID of the member to activate
     * @return void
     */
    public function activateMember(string $memberID): void
    {
        $this->db->query
            ->update('members')
            ->set(['active' => 'yes'])
            ->where('member_id', $memberID)
            ->execute();
    }
    /**
     * Sends an email with the specified data and template type
     *
     * @param array $data The data to use in the email template
     * @param string $templateType The type of email template to use
     * @return void
     */
    public function sendEmail(array $data, string $templateType)
    {
        $main = Messages::createEmailMessage('main', [$_SERVER['SERVER_NAME']]);

        $dynamic = Messages::createEmailMessage(
            $templateType,
            [
                $data['username'],
                $_SERVER['SERVER_NAME'],
                rand(),
                $data['encryptedID'],
                $data['active']
            ]
        );

        $body = str_replace('TEMPLATE', $dynamic, $main);

        $info = Messages::getEmailInfo($templateType, $data['recipient']);

        $this->mailer->sender($body, $info);
    }
    /**
     * Sets the reset token for the member with the specified ID
     *
     * @param string $memberID The ID of the member to set the reset token for
     * @param string $token The reset token to set
     * @return void
     */
    public function resetToken(string $memberID, string $token): void
    {
        $this->db->query
            ->update('members')
            ->set(['reset_token' => $token])
            ->where('member_id', $memberID)
            ->execute();
    }
    /**
     * Updates the password for the member with the specified email
     *
     * @param object $request The request object containing the password and email
     * @return void
     */
    public function newPassword($request): void
    {
        $this->db->query
            ->update('members')
            ->set(['password' => password_hash($request->password, PASSWORD_BCRYPT)])
            ->where('email', $request->email)
            ->execute();
    }
    /**
     * Sets the permission level for the member with the specified ID
     *
     * @param string $permission The permission level to set
     * @param string $memberID The ID of the member to set the permission level for
     * @return void
     */
    public function setPermission(string $permission, string $memberID): void
    {
        $this->db->query
            ->update('members')
            ->set(['permission' => $permission])
            ->where('member_id', $memberID)
            ->execute();
    }
    /**
     * Deletes the member with the specified ID
     *
     * @param string $memberID The ID of the member to delete
     * @return void
     */
    public function deleteMember(string $memberID): void
    {
        $this->db->query
            ->deleteFrom('members')
            ->where('member_id', $memberID)
            ->execute();
    }
    /**
     * Updates the member with the specified ID
     *
     * @param Member $member The member object containing the updated information
     * @return void
     */
    public function updateMember(Member $member): void
    {
        $set = [
            'username' => $member->username,
            'email' => $member->email,
            'avatar' => $member->avatar
        ];

        $this->db->query
            ->update('members')
            ->set($set)
            ->where('member_id', $member->memberID)
            ->execute();
    }
    /**
     * Updates the member information for the member with the specified ID
     *
     * @param Member $member The member object containing the updated information
     * @return void
     */
    public function updateInfoMember(Member $member): void
    {
        $set = [
            'member_name' => $member->name,
            'member_surname' => $member->surname,
            'visible' => $member->visible,
            'location' => $member->location,
            'age' => $member->age
        ];

        $this->db->query
            ->update('info')
            ->set($set)
            ->where('member', $member->memberID)
            ->execute();
    }
}
