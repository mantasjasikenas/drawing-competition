<?php

include("constants.php");

class MySQLDB
{

    var $connection;         //The MySQL database connection
    var $num_active_users;   //Number of active users viewing site
    var $num_active_guests;  //Number of active guests viewing site
    var $num_members;        //Number of signed-up users

    /* Note: call getNumMembers() to access $num_members! */

    /* Class constructor */

    function MySQLDB()
    {
        /* Make connection to database */
        $this->connection = mysqli_connect(DB_SERVER, DB_USER, DB_PASS, DB_NAME)
        or die(mysql_error() . '<br><h1>Faile include/constants.php suveskite savo MySQLDB duomenis.</h1>');

        /**
         * Only query database to find out number of members
         * when getNumMembers() is called for the first time,
         * until then, default value set.
         */
        $this->num_members = -1;

        if (TRACK_VISITORS) {
            /* Calculate number of users at site */
            $this->calcNumActiveUsers();

            /* Calculate number of guests at site */
            $this->calcNumActiveGuests();
        }
    }

    /**
     * confirmUserPass - Checks whether or not the given
     * username is in the database, if so it checks if the
     * given password is the same password in the database
     * for that user. If the user doesn't exist or if the
     * passwords don't match up, it returns an error code
     * (1 or 2). On success it returns 0.
     */
    function confirmUserPass($username, $password)
    {
        /* Add slashes if necessary (for query) */
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }

        /* Verify that user is in database */
        $q = "SELECT password FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return 1; //Indicates username failure
        }

        /* Retrieve password from result, strip slashes */
        $dbarray = mysqli_fetch_array($result);
        $dbarray['password'] = stripslashes($dbarray['password']);
        $password = stripslashes($password);

        /* Validate that password is correct */
        if ($password === $dbarray['password']) {
            return 0; //Success! Username and password confirmed
        } else {
            return 2; //Indicates password failure
        }
    }

    /**
     * confirmUserID - Checks whether or not the given
     * username is in the database, if so it checks if the
     * given userid is the same userid in the database
     * for that user. If the user doesn't exist or if the
     * userids don't match up, it returns an error code
     * (1 or 2). On success it returns 0.
     */
    function confirmUserID($username, $userid)
    {
        /* Add slashes if necessary (for query) */
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }

        /* Verify that user is in database */
        $q = "SELECT userid FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return 1; //Indicates username failure
        }

        /* Retrieve userid from result, strip slashes */
        $dbarray = mysqli_fetch_array($result);
        $dbarray['userid'] = stripslashes($dbarray['userid']);
        $userid = stripslashes($userid);

        /* Validate that userid is correct */
        if ($userid == $dbarray['userid']) {
            return 0; //Success! Username and userid confirmed
        } else {
            return 2; //Indicates userid invalid
        }
    }

    /**
     * usernameTaken - Returns true if the username has
     * been taken by another user, false otherwise.
     */
    function usernameTaken($username)
    {
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }
        $q = "SELECT username FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        return (mysqli_num_rows($result) > 0);
    }

    /**
     * usernameBanned - Returns true if the username has
     * been banned by the administrator.
     */
    function usernameBanned($username)
    {
        if (!get_magic_quotes_gpc()) {
            $username = addslashes($username);
        }
        $q = "SELECT username FROM " . TBL_BANNED_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        return (mysqli_num_rows($result) > 0);
    }

    /**
     * addNewUser - Inserts the given (username, password, email)
     * info into the database. Appropriate user level is set.
     * Returns true on success, false otherwise.
     */
    function addNewUser($username, $password, $email, $birth_date)
    {
        $time = time();
        /* If admin sign up, give admin user level */
        if (strcasecmp($username, ADMIN_NAME) == 0) {
            $ulevel = ADMIN_LEVEL;
        } else {
            $ulevel = USER_LEVEL;
        }

//        INSERT INTO " . TBL_USERS . " VALUES ('$username', '$password', '0', $ulevel, '$email', $time)";
        $q = "INSERT INTO users (username, password, email, userlevel, birth_date, timestamp) VALUES ('$username', '$password', '$email', $ulevel, '$birth_date', $time)";
        return mysqli_query($this->connection, $q);
    }

    /**
     * updateUserField - Updates a field, specified by the field
     * parameter, in the user's row of the database.
     */
    function updateUserField($username, $field, $value)
    {
        $q = "UPDATE " . TBL_USERS . " SET " . $field . " = '$value' WHERE username = '$username'";
        return mysqli_query($this->connection, $q);
    }

    /**
     * getUserInfo - Returns the result array from a mysql
     * query asking for all information stored regarding
     * the given username. If query fails, NULL is returned.
     */
    function getUserInfo($username)
    {
        $q = "SELECT * FROM " . TBL_USERS . " WHERE username = '$username'";
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        $dbarray = mysqli_fetch_array($result);
        return $dbarray;
    }

    /**
     * getNumMembers - Returns the number of signed-up users
     * of the website, banned members not included. The first
     * time the function is called on page load, the database
     * is queried, on subsequent calls, the stored result
     * is returned. This is to improve efficiency, effectively
     * not querying the database when no call is made.
     */
    function getNumMembers()
    {
        if ($this->num_members < 0) {
            $q = "SELECT * FROM " . TBL_USERS;
            $result = mysqli_query($this->connection, $q);
            $this->num_members = mysqli_num_rows($result);
        }
        return $this->num_members;
    }

    /**
     * calcNumActiveUsers - Finds out how many active users
     * are viewing site and sets class variable accordingly.
     */
    function calcNumActiveUsers()
    {
        /* Calculate number of users at site */
        $q = "SELECT * FROM " . TBL_ACTIVE_USERS;
        $result = mysqli_query($this->connection, $q);
        $this->num_active_users = mysqli_num_rows($result);
    }

    /**
     * calcNumActiveGuests - Finds out how many active guests
     * are viewing site and sets class variable accordingly.
     */
    function calcNumActiveGuests()
    {
        /* Calculate number of guests at site */
        $q = "SELECT * FROM " . TBL_ACTIVE_GUESTS;
        $result = mysqli_query($this->connection, $q);
        $this->num_active_guests = mysqli_num_rows($result);
    }

    /**
     * addActiveUser - Updates username's last active timestamp
     * in the database, and also adds him to the table of
     * active users, or updates timestamp if already there.
     */
    function addActiveUser($username, $time)
    {
        $q = "UPDATE " . TBL_USERS . " SET timestamp = '$time' WHERE username = '$username'";
        mysqli_query($this->connection, $q);

        if (!TRACK_VISITORS)
            return;
        $q = "REPLACE INTO " . TBL_ACTIVE_USERS . " VALUES ('$username', '$time')";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveUsers();
    }

    /* addActiveGuest - Adds guest to active guests table */

    function addActiveGuest($ip, $time)
    {
        if (!TRACK_VISITORS)
            return;
        $q = "REPLACE INTO " . TBL_ACTIVE_GUESTS . " VALUES ('$ip', '$time')";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveGuests();
    }

    /* These functions are self explanatory, no need for comments */

    /* removeActiveUser */

    function removeActiveUser($username)
    {
        if (!TRACK_VISITORS)
            return;
        $q = "DELETE FROM " . TBL_ACTIVE_USERS . " WHERE username = '$username'";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveUsers();
    }

    /* removeActiveGuest */

    function removeActiveGuest($ip)
    {
        if (!TRACK_VISITORS)
            return;
        $q = "DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE ip = '$ip'";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveGuests();
    }

    /* removeInactiveUsers */

    function removeInactiveUsers()
    {
        if (!TRACK_VISITORS)
            return;
        $timeout = time() - USER_TIMEOUT * 60;
        $q = "DELETE FROM " . TBL_ACTIVE_USERS . " WHERE timestamp < $timeout";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveUsers();
    }

    /* removeInactiveGuests */

    function removeInactiveGuests()
    {
        if (!TRACK_VISITORS)
            return;
        $timeout = time() - GUEST_TIMEOUT * 60;
        $q = "DELETE FROM " . TBL_ACTIVE_GUESTS . " WHERE timestamp < $timeout";
        mysqli_query($this->connection, $q);
        $this->calcNumActiveGuests();
    }

    /**
     * query - Performs the given query on the database and
     * returns the result, which may be false, true or a
     * resource identifier.
     */
    function query($query)
    {
        return mysqli_query($this->connection, $query);
    }

    function getCurrentCompetitionPaintings(): ?array
    {
        $cur = $this->getCurrentCompetition();

        $q = "SELECT * FROM paintings WHERE fk_upload IN (SELECT id FROM uploads WHERE fk_competition = " . $cur['id'] . ")";
        $result = mysqli_query($this->connection, $q);

        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    function getAvailableCompetitions(): ?array
    {
        $q = "SELECT * FROM competitions WHERE start_date <= NOW() AND end_date >= NOW()";
        $result = mysqli_query($this->connection, $q);

        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    function getCurrentCompetition()
    {
        $q = "SELECT * FROM " . TBL_COMPETITIONS . " WHERE start_date < NOW() AND end_date > NOW()";
        $result = mysqli_query($this->connection, $q);
        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return mysqli_fetch_array($result);
    }

    function getTop10Paintings(): ?array
    {

        $q = "SELECT paintings.id AS painting_id,
                   avg_score AS score,
                   image,
                   style,
                   username,
                   birth_date
            FROM (SELECT fk_painting,
                         (SUM(composition +
                              colorfulness +
                              compliance +
                              originality) / (4 * COUNT(*))) AS avg_score
                  FROM reviews
                  GROUP BY fk_painting
                  ORDER BY avg_score DESC) as scoring
                     INNER JOIN paintings ON paintings.id = fk_painting
                     INNER JOIN uploads ON uploads.id = fk_upload
                     INNER JOIN users u on u.id = uploads.fk_user
            ORDER BY avg_score DESC, u.birth_date DESC
            LIMIT 10;";

        $result = mysqli_query($this->connection, $q);

        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }

        /* Return result array */
        $arr = mysqli_fetch_all($result, MYSQLI_ASSOC);

        $position = 1;
        foreach ($arr as $key => $value) {
            $arr[$key]['place'] = $position++;
        }

        return $arr;
    }

    function getUnratedPaintingsId($userid): ?array
    {
        $q = "SELECT id FROM paintings
            WHERE id NOT IN (SELECT fk_painting FROM reviews WHERE fk_user = $userid) AND 
                  id NOT IN (SELECT fk_painting FROM reports WHERE fk_user = $userid)
            ORDER BY id";
        $result = mysqli_query($this->connection, $q);

        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }

        /* Fetch entire result array */
        $assocArray = mysqli_fetch_all($result, MYSQLI_ASSOC);

        /* Using array_column to get only the ids from the associative array */
        /* Return result array */
        return array_column($assocArray, 'id');
    }

    function reportPainting($painting_id, $user_id, $cause): bool
    {
        $q = "INSERT INTO reports (fk_painting, fk_user, cause, creation_date) VALUES ($painting_id, $user_id, '$cause', NOW())";
        $result = mysqli_query($this->connection, $q);

//        echo mysqli_error($this->connection);

        return $result;
    }

    function getUserPaintingsAndScores($userid): ?array
    {
        $q =
            "SELECT paintings.id AS painting_id,
          IFNULL(avg_score,0) AS score,
          IFNULL(avg_composition,'-') AS composition,
          IFNULL(avg_colorfulness,'-') AS colorfulness,
          IFNULL(avg_compliance,'-') AS compliance,
          IFNULL(avg_originality,'-') AS originality,
          image,
          style
       FROM paintings
       LEFT JOIN uploads ON uploads.id = fk_upload
       LEFT JOIN users u on u.id = uploads.fk_user
       LEFT JOIN (SELECT fk_painting,
                         AVG(composition) AS avg_composition,
                        AVG(colorfulness) AS avg_colorfulness,
                        AVG(compliance) AS avg_compliance,
                        AVG(originality) AS avg_originality,
                        (SUM(composition +
                             colorfulness +
                             compliance +
                             originality) / (4 * COUNT(*))) AS avg_score
                  FROM reviews
                  GROUP BY fk_painting) as scoring 
             ON paintings.id = scoring.fk_painting
       WHERE u.id = $userid
       ORDER BY score DESC, u.birth_date DESC";

//        $statement = $this->connection->prepare($q);
//        $statement->bind_param('i', $userid);
//
//        $statement->execute();
//
//        $result = $statement->get_result();

        $result = mysqli_query($this->connection, $q);

        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    function getUnsolvedReports(): ?array
    {
        $q = "SELECT reports.id          AS report_id,
                       reports.fk_user     AS user_id,
                       reports.fk_painting AS painting_id,
                       reports.creation_date,
                       reports.cause       AS cause,
                       users.username,
                       paintings.image     AS image,
                       uploads.style       AS style
                FROM reports
                         INNER JOIN users ON reports.fk_user = users.id
                         INNER JOIN paintings ON reports.fk_painting = paintings.id
                         INNER JOIN uploads ON uploads.id = fk_upload";
        $result = mysqli_query($this->connection, $q);

        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    function createCompetition($topic, $start_date, $end_date, $imgContent): bool
    {
        $q = "INSERT INTO competitions (topic, start_date, end_date, creation_date, image) VALUES ('$topic', '$start_date', '$end_date', NOW(), ?)";

        $statement = $this->connection->prepare($q);
        $statement->bind_param('s', $imgContent);

        return $statement->execute();
    }

    function getUploadImages($upload_id): ?array
    {
        $q = "SELECT * FROM paintings WHERE fk_upload = $upload_id";
        $result = mysqli_query($this->connection, $q);

        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    function getPaintingById($id)
    {
        $q = "SELECT * FROM paintings
                INNER JOIN uploads ON uploads.id = fk_upload
                WHERE paintings.id = $id
        ";
        $result = mysqli_query($this->connection, $q);

        /* Error occurred, return given name by default */
        if (!$result || (mysqli_num_rows($result) < 1)) {
            return NULL;
        }
        /* Return result array */
        return mysqli_fetch_array($result);
    }

    function createReview($user_id, $painting_id, $composition, $colorfulness, $compliance, $originality)
    {
        $q = "INSERT INTO reviews (fk_user, fk_painting, composition, colorfulness, compliance, originality, creation_date) VALUES ($user_id, $painting_id, $composition, $colorfulness, $compliance, $originality, NOW())";
        return mysqli_query($this->connection, $q);
    }

}

/* Create database connection */
$database = new MySQLDB;
