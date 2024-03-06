<?php

// Hide errors
error_reporting(0);


// Use PHPMailer classes
use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

// Include the connection file
require "connection.php";

// Include the Composer autoloader
require "vendor/autoload.php";

// Include admin crud
require "admin-model.php";

/*
 * CRUD class
 * This class will handle all CRUD operations
 * from the client.
 */
class Crud extends AdminCrud
{
    // Database connection
    private $conn;

    // Constructor
    public function __construct($db)
    {
        $this->conn = $db;
    }

    // Create new user
    public function create_new_user($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $petition = $petition->petition->data->create_new_user;
        $user_name = $petition->user_name;
        $user_email = $petition->user_email;
        $password = $petition->password;
        
        $invalid_user = self::check_user($user_email);
        if ( $invalid_user ):
            return $invalid_user;
        endif;

        // Create new user
        $query = 'INSERT INTO Users SET
         created_at = :created_at,
         user_name = :user_name,
         user_email = :user_email,
         password = :password,
         ip = :ip
        ';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":created_at", date("Y-m-d H:i:s"));
        $stmt->bindParam(":user_name", $user_name);
        $stmt->bindParam(":user_email", $user_email);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);
        
        // Todo: validate send email
        // $petition->user_email_type = "wellcome_email";
        // $wellcome_email = self::send_email($petition);
        // if(json_decode($wellcome_email)->status === "error"):
        //     return $wellcome_email;
        // endif;

        $result = $stmt->execute();
        if (!$result):
            return '{
                "status": "error",
                "message": "User not created"
            }';
        endif;

        // Get id user 
        $query = "SELECT * FROM Users WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $user_email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Creating suscription
        $user = json_decode(json_encode($user));
        return self::create_new_suscription($user);
        
    }

    // Get user
    public function get_user($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        
        $user = $petition->petition->data->get_user;

        // Geting user
        $query = "SELECT * FROM Users WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $user->user_email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result):
            return '{
                "status": "error",
                "message": "User not found"
            }';
        endif;
        
        // Return user
        return '{
            "status": "success",
            "message": "User found",
            "data": '.json_encode($result).'
        }';

    }

    // Create new suscription
    private function create_new_suscription($user)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);

        $invalid_suscription = self::check_user_suscription($user->user_email);
        if ( $invalid_suscription ):
            return $invalid_suscription;
        endif;

        // Create new suscription
        $query = "INSERT INTO Suscriptions SET 
        created_at = :created_at,
        user_id = :user_id,
        user_name = :user_name, 
        user_email = :user_email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user->id);
        $stmt->bindParam(":created_at", date("Y-m-d H:i:s"));
        $stmt->bindParam(":user_name", $user->user_name);
        $stmt->bindParam(":user_email", $user->user_email);
        
        // // Send suscription email
        // $petition->user_email_type = "suscription_email";
        // $email = self::send_email($petition);
        // if( json_decode( $email )->status === "error" ):
        //     return $email;
        // endif;

        // Execute query
        $result = $stmt->execute();

        if (!$result):
            return '{
                "status": "error",
                "message": "Suscription not created"
            }';
        endif;

        // Return success
        unset($user->password);
        unset($user->ip);
        return '{
            "status": "success",
            "message": "Suscription created successfully",
            "data": '.json_encode($user).'
        }';

    }

    // Get suscription
    public function get_suscription($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $suscription = $petition->petition->data->get_suscription;

        // Get suscription
        $query = "SELECT * FROM Suscriptions WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $suscription->user_email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$result):
            return '{
                "status": "error",
                "message": "Suscription not found"
            }';
        endif;

        // Return suscription
        return '{
            "status": "success",
            "message": "Suscription found",
            "data": '.json_encode($result).'
        }';

    }

    // Get suscriptions
    public function get_suscriptions($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $suscriptions = $petition->petition->data->get_suscriptions;
        $limit = $suscriptions->limit;
        $offset = $suscriptions->offset;

        // Get suscriptions
        $query = "SELECT * FROM Suscriptions LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results):
            return '{
                "status": "error",
                "message": "Suscriptions not found"
            }';
        endif;

        // Return suscriptions
        return '{
            "status": "success",
            "message": "Suscriptions found",
            "data": '.json_encode($results).'
        }';

    }

    // Update suscription
    public function update_suscription( $petition ){

        $suscription = $petition->petition->data->update_suscription;

        // Todo : Validate role user

        $check_suscription = self::check_user_suscribed( $suscription->user_email );
        if( json_decode( $check_suscription )->status === "error" ):
            return $check_suscription;
        endif;
        
        if( json_decode( $check_suscription )->type !== "trial" ):
            return '{
                "status": "error",
                "message": "Suscription actived , you cant not update it."
            }';
        endif;

        // Update suscription
        $query = "UPDATE Suscriptions SET 
        user_email = :user_email,
        ds_date = :ds_date,
        ds_expiry = :ds_expiry,
        ds_amount = :ds_amount,
        ds_merchant_suscription_start_date = :ds_merchant_suscription_start_date,
        ds_merchant_matching_data = :ds_merchant_matching_data
        WHERE user_email = :user_email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $suscription->user_email);
        $stmt->bindParam(":ds_date", $suscription->ds_date);
        $stmt->bindParam(":ds_expiry", $suscription->ds_expiry);
        $stmt->bindParam(":ds_amount", $suscription->ds_amount);
        $stmt->bindParam(":ds_merchant_suscription_start_date", $suscription->ds_merchant_suscription_start_date);
        $stmt->bindParam(":ds_merchant_matching_data", $suscription->ds_merchant_matching_data);
        $result = $stmt->execute();

        if (!$result):
            return '{
                "status": "error",
                "message": "Suscription not updated"
            }';
        endif;

        // Return success
        return '{
            "status": "success",
            "message": "Suscription updated successfully ( trial case ) "
        }';
        
    }

    // Delete suscription
    public function delete_suscription($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $suscription = $petition->petition->data->delete_suscription;

        // Delete suscription
        $query = "DELETE FROM Suscriptions WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $suscription->user_email);
        $stmt->execute();
        $rows = $stmt->rowCount();

        if ($rows < 1):
            return '{
                "status": "error",
                "message": "Suscription not deleted, suscription not found."
            }';
        endif;

        // Return success
        return '{
            "status": "success",
            "message": "Suscription deleted successfully"
        }';
    }

    // Down suscription
    public function down_suscription($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $suscription = $petition->petition->data->down_suscription;

        $check_suscription = self::check_user_suscribed( $suscription->user_email );
        if( json_decode( $check_suscription )->status === "error" ):
            return $check_suscription;
        endif;

        if(json_decode($check_suscription)->type === "down"):
            return '{
                "status": "error",
                "message": "Suscription already down"
            }';
        endif;

        if(json_decode($check_suscription)->type === "canceled"):
            return '{
                "status": "error",
                "message": "Suscription already canceled."
            }';
        endif;

        // Douwn suscription
        $query = "UPDATE Suscriptions 
        SET status = :status
        canceled_date = :canceled_date 
        reason = :reason
        WHERE user_email = :user_email";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $suscription->user_email);
        $stmt->bindParam(":reason", "user request");
        $stmt->bindParam(":status", 'down');
        $stmt->bindParam(":canceled_date", date("Y-m-d H:i:s"));
        
        if( json_decode( $check_suscription )->type === "trial" ):
            
            $stmt->bindParam(":reason", "trial time status");
            $result = $stmt->execute();
            if (!$result):
                return '{
                    "status": "error",
                    "message": "Suscription not down."
                }';
            endif;

            // Return success
            return '{
                "status": "success",
                "message": "Suscription down successfully ( user request trial )"
            }';

        endif;
        
        $stmt->bindParam(":reason", "active status");
        $result = $stmt->execute();
        
        if(!$result){
            // Return error
            return '{
                "status": "error",
                "message": "Suscription not down."
            }';
        }

        // Return success
        return '{
            "status": "success",
            "message": "Suscription down successfully"
        }';
        
    }

    // Check user exists
    private function check_user($user_email)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        
        // Check if user exists
        $query = "SELECT * FROM Users
        WHERE user_email = :user_email";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $user_email);
        $stmt->execute();
        $num = $stmt->rowCount();

        if ( $num > 0 ):
            return '{
                "status": "error",
                "message": "User already exists."
            }';
        endif;

        return false;
    }

    // Check user suscription or exists
    private function check_user_suscription($user_email)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        
        // Check if user exists
        $query = "SELECT * FROM Suscriptions WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $user_email);
        $stmt->execute();
        $num = $stmt->rowCount();

        if ( $num > 0 ):
            return '{
                "status": "error",
                "message": "User already exists."
            }';
        endif;

        return false;
    }

    // Check user suscribed
    private function check_user_suscribed( $user_email )
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        
        // Check if user exists
        $query = "SELECT * FROM Suscriptions WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $user_email);
        $stmt->execute();
        $result = $stmt->fetch(PDO::FETCH_ASSOC);

        if ( count($result) < 1 ):
            return '{
                "status": "error",
                "message": "User not suscribed."
            }';
        endif;
        
        $result = json_decode(json_encode($result));

        if( $result->status === "down" ):
            return '{
                "status": "error",
                "type": "down",
                "message": "User suscription is down."
            }';
        endif;

        if( $result->status === "trial" ):
            return '{
                "status": "success",
                "type": "trial",
                "message": "User suscription is active, in trial."
            }';
        endif;

        if( $result->status === "active" ):
            return '{
                "status": "success",
                "type": "active",
                "message": "User suscription is active."
            }';
        endif;

        return false;

    }

    // Get users
    public function get_users($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $users = $petition->petition->data->get_users;
        $limit = $users->limit;
        $offset = $users->offset;

        // Geting users
        $query = "SELECT * FROM Users LIMIT :limit OFFSET :offset";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":limit", $limit, PDO::PARAM_INT);
        $stmt->bindParam(":offset", $offset, PDO::PARAM_INT);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results):
            return '{
                "status": "error",
                "message": "Users not found"
            }';
        endif;

        // Return users
        return '{
            "status": "success",
            "message": "Users found",
            "data": '.json_encode($results).'
        }';

    }

    // Update user
    public function update_user_password($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $user = $petition->petition->data->update_user_password;

        // Validate user exists
        $valid_user = self::check_user($user->user_email);
        if (!$valid_user):
            return $valid_user;
        endif;

        // Update user
        $query = "UPDATE Users SET password = :password WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $user->user_email);
        $stmt->bindParam(":password", $user->password);
        $result = $stmt->execute();

        if (!$result):
            return '{
                "status": "error",
                "message": "User password not updated"
            }';
        endif;

        // Return success
        return '{
            "status": "success",
            "message": "User updated password successfully"
        }';

    }

    // Delete user
    public function delete_user($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $user = $petition->petition->data->delete_user;

        // Delete user
        $query = "DELETE FROM Users WHERE id = :user_id";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_id", $user->user_id);
        $stmt->execute();

        // Return success
        return '{
            "status": "success",
            "message": "User deleted successfully"
        }';
        
    }

    // Send email
    private function send_email($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);

        // Retrieve petition data
        $mail = $petition->petition->data;

        // Include config file
        require_once "config.php";

        // Strore vars
        $to = $mail->to;
        // Validate email format
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)):
            return '{
                "status": "error",
                "message": "Invalid email format"
            }';
        endif;
        $subject = $mail->subject;
        $email = $mail->user_email;
        $message = $mail->message;
        $user_name = $mail->user_name;
        $email_type = $mail->user_email_type;

        $server_name = $_SERVER["SERVER_NAME"];
        $support_email = "support@".$server_name;
        
        $server_ucfirst = ucfirst(
            explode(".com", explode("@", $support_email)[0])[0]
        );

        // Create new PHPMailer instance
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = "hd-europe2124.banahosting.com";
        $mail->Port = 465;
        $mail->Charset = "UTF-8";

        // Set SMTP security
        $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
        $mail->SMTPAuth = true;
        $mail->Username = $support_email;
        $mail->Password = $smtp_password;

        // Validating type of email
        if ($email_type === "wellcome_email"):
            $message = file_get_contents("wellcome_email.html");
            $message = str_replace("{{user_name}}", $user_name, $message);
            $message = str_replace("{{user_email}}", $email, $message);
            $message = str_replace("{{server_name}}", $server_name, $message);
            $message = str_replace(
                "{{support_email}}",
                $support_email,
                $message
            );
        endif;

        if ($email_type === "suscription_email"):
            $message = file_get_contents("suscription_email.html");
            $message = str_replace("{{user_name}}", $user_name, $message);
            $message = str_replace("{{user_email}}", $email, $message);
            $message = str_replace("{{server_name}}", $server_name, $message);
            $message = str_replace(
                "{{support_email}}",
                $support_email,
                $message
            );
        endif;

        if ($email_type === "reset_password_email"):
            $message = file_get_contents("reset_password_email.html");
            $message = str_replace("{{user_name}}", $user_name, $message);
            $message = str_replace("{{user_email}}", $email, $message);
            $message = str_replace(
                "{{password}}",
                $petition->password,
                $message
            );
            $message = str_replace("{{server_name}}", $server_name, $message);
            $message = str_replace(
                "{{support_email}}",
                $support_email,
                $message
            );
        endif;

        // Set email parameters
        if (
            $email_type === "wellcome_email" ||
            $email_type === "suscription_email" ||
            $email_type === "reset_password_email"
        ):
            $mail->isHTML(true);
            $mail->setFrom($support_email, $server_ucfirst);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $message;
        elseif ($email_type === "support_email"):
            $mail->setFrom($email, $user_name);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->Body = $message;
        else:
            return '{
                "status": "error",
                "message": "Invalid email type"
            }';
        endif;

        // Send email
        if ($mail->send()):
            self::save_email_log($petition, $message, true);
            return '{
                "status": "success",
                "message": "Email sent successfully"
            }';
        else:
            self::save_email_log($petition, $message, false);
            return '{
                "status": "error",
                "message": "Email not sent"
            }';
        endif;

    }

    // Save email log
    private function save_email_log($petition, $message, $status)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $email_log = $petition->petition->data;

        // Store vars
        $to = $email_log->to;
        $subject = $email_log->subject;
        $user_email = $email_log->user_email;
        $message = $email_log->message;
        $user_name = $email_log->user_name;
        $email_type = $email_log->user_email_type;

        // Save email log
        $query = 'INSERT INTO EmailLogs
        SET to = :to, subject = :subject,
        user_email = :user_email, message = :message,
        user_name = :user_name, email_type = :email_type,
        ip = :ip, status = :status';

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $user_email);
        $stmt->bindParam(":user_name", $user_name);
        $stmt->bindParam(":to", $to);
        $stmt->bindParam(":subject", $subject);
        $stmt->bindParam(":email_type", $email_type);
        $stmt->bindParam(":message", $message);
        $stmt->bindParam(":status", $status);
        $stmt->bindParam(":ip", $_SERVER["REMOTE_ADDR"]);
        $result = $stmt->execute();

        if ($result):
            return '{
                "status": "success",
                "message": "Email log saved successfully"
            }';
        else:
            return '{
                "status": "error",
                "message": "Email log not saved"
            }';
        endif;
    }

    // Get search
    public function get_searchs($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $searchs = $petition->petition->data->get_searchs;

        // Get search
        $query = "SELECT searchs FROM Users WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $searchs->user_email);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$results):
            return '{
                "status": "error",
                "message": "Searchs not found"
            }';
        endif;

        // Return search
        return '{
            "status": "success",
            "message": "Search found",
            "data": '.json_encode($results).'
        }';

    }

    // Update searchs
    public function update_searchs($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $searchs = $petition->petition->data->update_searchs;

        // Update searchs
        $query = "UPDATE Users SET searchs = :searchs WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $searchs->id);
        $stmt->bindParam(":searchs", $searchs->searchs);
        $result = $stmt->execute();

        if (!$result):
            return '{
                "status": "error",
                "message": "Searchs not updated"
            }';
        endif;

        // Return success
        return '{
            "status": "success",
            "message": "Searchs updated successfully"
        }';
    }

    // Get downloads
    public function get_downloads($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $downloads = $petition->petition->data->get_downloads;

        // Get downloads
        $query = "SELECT downloads FROM Users WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":user_email", $downloads->user_email);
        $stmt->execute();
        $downloads = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (!$downloads):
            return '{
                "status": "error",
                "message": "Downloads not found"
            }';
        endif;

        // Return downloads
        return '{
            "status": "success",
            "message": "Downloads found",
            "data": '.json_encode($downloads).'
        }';

    }

    // Update downloads
    public function update_downloads($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $downloads = $petition->petition->data->update_downloads;

        // Update downloads
        $query = "UPDATE Users SET downloads = :downloads WHERE user_email = :user_email";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":downloads", $downloads->downloads);
        $stmt->bindParam(":user_email", $downloads->user_email);
        $result = $stmt->execute();

        if (!$result):
            return '{
                "status": "error",
                "message": "Downloads not updated"
            }';
        endif;

        // Return success
        return '{
            "status": "success",
            "message": "Downloads updated successfully"
        }';

    }

    // Create right to be forgotten
    public function create_right_to_beforgotten($petition)
    {
        // Sanitize petition
        //$petition = self::sanitize_petition($petition);
        $forgotten = $petition->petition->data->create_right_to_beforgotten;

        // Create right to be forgotten
        $query = "INSERT INTO RightToBeForgotten SET 
        user_email = :user_email,
        created_at = :created_at,
        user_email = :user_email,
        status = :status,
        forgotten_name = :forgotten_name,
        forgotten_reason = :forgotten_reason";

        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(":created_at", date("Y-m-d H:i:s"));
        $stmt->bindParam(":user_email", $forgotten->user_email);
        $stmt->bindParam(":status", $forgotten->status);
        $stmt->bindParam(":forgotten_name", $forgotten->forgotten_name);
        $stmt->bindParam(":forgotten_reason", $forgotten->forgotten_reason);
        $result = $stmt->execute();

        if (!$result):
            return '{
                "status": "error",
                "message": "Right to be forgotten not created"
            }';
        endif;

        // Return success
        return '{
            "status": "success",
            "message": "Right to be forgotten created successfully"
        }';

    }
    
    // Sanitize petition
    private function sanitize_petition($petition)
    {
        $petition = json_decode(json_encode($petition), true);
        foreach ($petition as $key => $value) {
            $petition[$key] = htmlspecialchars(strip_tags($value));
        }
   
        return $petition;
    }


}

$Crud = new Crud($db);
