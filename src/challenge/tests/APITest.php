<?php

require_once realpath(__DIR__.'/../config.php');
require_once realpath(__DIR__.'/../utils.php');
use PHPUnit\Framework\TestCase;

$HOST = '127.0.0.1/src/challenge';

class APITest extends TestCase
{
    /** 
     * @dataProvider userProvider 
    */
    public function testCreate()
    {
        global $HOST;

        $args = func_get_args();

        $user = [
            "name" => $args[0],
            "lastname" => $args[1],
            "phone" => $args[2],
        ];

        $response = post($HOST.'/create_user.php', $user);
        $json = json_decode($response, true);
        $this->assertSame($json["status_code"], 201);
    }

    /**
     * @depends testCreate 
    */
    public function testGetUser() 
    {
        global $mysqli, $HOST;

        $users_test_set = $this->userProvider();

        $query = "SELECT COUNT(*) as user_count FROM user";
        $result = $mysqli->query($query);
        $row = $result->fetch_assoc();
        $this->assertSame(count($users_test_set), intval($row["user_count"]));

        $users = json_decode(get($HOST.'/users.php'), true);
        $this->assertSame(count($users_test_set), count($users["body"]));

        $key = array_rand($users_test_set, 1);
        $sample = $users_test_set[$key];

        $query = sprintf("SELECT * FROM user WHERE phone='%s'", $sample[2]);
        $result = $mysqli->query($query);
        $this->assertSame($result->num_rows, 1);
        
        $row = $result->fetch_assoc();
        $this->assertSame($row["name"], $sample[0]);
        $this->assertSame($row["lastname"], $sample[1]);
    }


    /**
     * @dataProvider contactProvider
     * @depends testCreate 
    */
    public function testAddContact() 
    {
        global $mysqli, $HOST;

        $args = func_get_args();
        $users_test_set = $this->userProvider();
        
        $key = array_rand($users_test_set, 1);
        $sample = $users_test_set[$key];
        
        $query = sprintf("SELECT * FROM user WHERE phone='%s'", $sample[2]);
        $result = $mysqli->query($query);
        $this->assertSame($result->num_rows, 1);

        $row = $result->fetch_assoc();
        $data = [
            "user_id" => $row["id"],
            "contact_name" => $args[0],
            "contact_phone_number" => $args[1]
        ];
        $response = post($HOST.'/contact.php', $data);
        $json = json_decode($response, true);
        $this->assertSame($json["status_code"], 201);
    }

    
    /**
     * @depends testAddContact 
    */
    public function testGetContacts() 
    {
        global $mysqli, $HOST;

        $contact_test_set = $this->contactProvider();

        $query = "SELECT COUNT(*) as contact_count FROM contact";
        $result = $mysqli->query($query);
        $row = $result->fetch_assoc();
        $this->assertSame(count($contact_test_set), intval($row["contact_count"]));

        $query = "SELECT *, (SELECT COUNT(*) FROM contact WHERE user_id=user.id) as user_contact_count FROM user";
        $results = $mysqli->query($query);
        while($row = $results->fetch_assoc()) 
        {
            $contacts = json_decode(get($HOST.'/contact.php?user_id='.$row["id"]), true);
            $this->assertSame(count($contacts["body"]), intval($row["user_contact_count"]));
        }
    }


    /**
     * @dataProvider commonContactProvider  
    */    
    public function testAddCommonContacts() 
    {
        global $mysqli, $HOST;
        
        $args = func_get_args();
        
        $query = sprintf("SELECT id FROM user WHERE phone='%s'", $args[0]);
        $result = $mysqli->query($query);
        $row = $result->fetch_assoc();

        $data = [
            "user_id" => $row["id"],
            "contact_name" => $args[1],
            "contact_phone_number" => $args[2]
        ];
        $response = post($HOST.'/contact.php', $data);
        $json = json_decode($response, true);
        $this->assertSame($json["status_code"], 201);
    }

    /**
     * @depends testAddCommonContacts 
    */
    public function testCommonContacts() 
    {
        global $mysqli, $HOST;

        $common_contact_test_set = $this->commonContactProvider();
        $users = [];
        $common_contacts = [];

        foreach($common_contact_test_set as $key => $values) 
        {
            if(!in_array($values[0], $users)) 
            {
                $users[] = $values[0];
            }
            $common_contacts[$values[2]][] = [$values[0], $values[1]];
        }
        
        $query = sprintf("SELECT id FROM user WHERE phone='%s' OR phone='%s'", $users[0], $users[1]);
        $result =$mysqli->query($query);
        $row = $result->fetch_all(MYSQLI_ASSOC);

        $response = get($HOST.sprintf("/common_contacts.php?user1='%s'&user2='%s'", $row[0]["id"], $row[1]["id"]));
        $json = json_decode($response, true);        

        $in_common_cnt = 0;
        foreach($common_contacts as $key => $value) 
        {
            if(count($value) == 2) 
            {
                $in_common_cnt += 1;
            }
        }
        $this->assertSame(count($json["body"]), $in_common_cnt);

        for($i = 0; $i < count($json["body"]); $i++) 
        {
            $this->assertSame(in_array($json["body"][$i]["contact_phone_number"], array_keys($common_contacts)), true);
        }
    }


    /**
     * @dataProvider contactUpdateProvider
     * @depends testAddContact
     * @depends testCommonContacts
    */
    public function testUpdateContact() 
    {
        global $mysqli, $HOST;

        $args = func_get_args();
        $contact_test_set = $this->contactProvider();
        $key = array_rand($contact_test_set, 1);
        $sample = $contact_test_set[$key];

        $query = sprintf("SELECT * FROM contact WHERE contact_phone_number='%s' LIMIT 1", $sample[1]);
        $result = $mysqli->query($query);
        $cnt = $result->num_rows;

        $row = $result->fetch_assoc();

        $data = [
            "contact_id" => $row["id"],
            "user_id" => $row["user_id"],
            "contact_name" => $args[0],
            "contact_phone_number" => $args[1]
        ];
        $response = put($HOST.'/contact.php', $data);
        $json = json_decode($response, true);

        if($cnt) 
        {  
            $this->assertSame($json["status_code"], 200);
        }
        
        else 
        {
            $this->assertSame($json["status_code"], 400);
            $this->assertSame($data["contact_id"], null);
            $this->assertSame($data["user_id"], null);
        }
    }

    public function userProvider() 
    {
        return [
            "Case_0" => ["Arthur", "Morgan", "+34640382371"],
            "Case_1" => ["Samanosuke", "Akechi", "+34640382372"],
            "Case_2" => ["Sylvanas", "Windrunner", "+34640382373"],
            "Case_3" => ["Ratchet", "", "+34640382374"],
            "Case_4" => ["Dorian", "Pavus", "+34640382375"],
            "Case_5" => ["Alduin", "", "+34640382376"],
            "Case_6" => ["Niko", "Bellic", "+34640382377"],
            "Case_7" => ["Grom", "Hellscream", "+34640382378"],
            "Case_8" => ["Kratos", "", "+34640382379"],
            "Case_9" => ["Ezio", "Auditore", "+34640382370"],
        ];
    }
    
    public function contactProvider() 
    {
        return [
            "Case_0" => ["Jade", "+34641382371"],
            "Case_1" => ["Gomez", "+34641382372"],
            "Case_2" => ["Tommy", "+34642382373"],
            "Case_3" => ["Sam", "+34643382374"],
            "Case_4" => ["Gordon", "+34644382375"],
            "Case_5" => ["Trevor", "+34640392376"],
            "Case_6" => ["Ellie", "+34641382371"],
            "Case_7" => ["Grom", "+34642382373"],
            "Case_8" => ["John", "+34640382579"],
            "Case_9" => ["Bowser", "+34640387370"],
        ];
    }

    public function contactUpdateProvider() 
    {
        return [
            "Case_0" => ["Lara", "+34641392371"],
            "Case_1" => ["Donkey", "+34641382372"],
            "Case_2" => ["Yoshi", "+34652382373"],
            "Case_3" => ["Link", "+34643383374"],
            "Case_4" => ["Mario", "+34644381375"],
        ];
    }

    public function commonContactProvider() 
    {
        return [
            "Case_0" => ["+34640382371", "Thrall", "+34641395371"],
            "Case_1" => ["+34640382371", "Illidan", "+34647382372"],
            "Case_2" => ["+34640382371", "Arthas", "+34651382373"],
            "Case_3" => ["+34640382372", "Jaina", "+34641395371"],
            "Case_4" => ["+34640382372", "Anduin", "+34651382373"],
        ];
    }
}


?>