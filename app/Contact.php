<?php

namespace App;

use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Contact extends Model
{
    const CONTACT_FIELDS = [
        'team_id' => 'Team Id',
        'name' => 'Name',
        'phone' => 'Phone',
        'email' => 'Email',
        'sticky_phone_number_id' => 'Sticky Phone Number Id'
    ];


    /**
     * Executes a mass insert/update on many contact rows
     *
     * @param Array $contacts            Array of contacts to insert
     * @param Array $keyMap              Array of keys that map to the contact fields
     * @param Array $customAttributeKeys Array of keys for custom attributes
     * @param Array $headers             Array of headers for custom attributes keys
     *
     * @return void
     */
    public static function massInsertOrUpdate(
        $contacts,
        $keyMap,
        $customAttributeKeys,
        $headers
    ) {
        /**
         * I decided to use the direct PDO implmentation here because of the fact that the
         * Laravel updateOrCreate implementation makes 2 sql calls, 1 to check if a record exists,
         * the second to either update or create.  This handles both calls for us.
         */
        $query = "INSERT INTO contacts
            (team_id, name, phone, email, sticky_phone_number_id, created_at)
            VALUES (?, ?, ?, ?, ?, ?)
            ON DUPLICATE KEY UPDATE
            name=VALUES(name),
            email=VALUES(email),
            sticky_phone_number_id=VALUES(sticky_phone_number_id),
            updated_at=NOW(),
            id=LAST_INSERT_ID(id);";

        $statement = DB::getPdo()->prepare($query);
        $errors = [];
        foreach ($contacts as $contact) {
            $values = [];
            $values[] = $contact[$keyMap['team_id']];
            $values[] = $contact[$keyMap['name']];
            $values[] = $contact[$keyMap['phone']];
            $values[] = $contact[$keyMap['email']];
            $values[] = $contact[$keyMap['sticky_phone_number_id']];
            $values[] = Carbon::now();
            try{
                $statement->execute($values);
            } catch(Exception $e)
            {
                Log::warning($e);
                array_push($errors, $contact);
            }
            $id = DB::getPdo()->lastInsertId();
            //Now that we have inserted/updated the contact, process the custom attributes
            Self::_processCustomAttributes(
                $id,
                $customAttributeKeys,
                $headers,
                $contact
            );
        }
        return $errors;

    }

    /**
     * Processes custom attributes for contacts
     *
     * @param int   $id                  id of contact
     * @param Array $customAttributeKeys An array custom attribute keys.
     * @param Array $headers             An array of the headers for the key fields
     * @param Array $contact             An array of contacts
     *
     * @return void
     */
    private static function _processCustomAttributes($id, $customAttributeKeys, $headers, $contact) {
        /**
         * In reality, this function should probably work like the contact one, with the query running
        * through pdo with a unique key to prevent additonal queries
        */
        $customAttributes = [];
        foreach ($customAttributeKeys as $customAttributeKey) {
            $customAttribute['contact_id'] = $id;
            $customAttribute['key'] = $headers[$customAttributeKey];
            $customAttribute['value'] = $contact[$customAttributeKey];
            array_push($customAttributes, $customAttribute);
        }
        CustomAttribute::insert($customAttributes);
    }
}
