<?php
  require_once 'model/databaseHandler.class.php';
  require_once 'model/User.class.php';
  require_once 'model/Security.class.php';
  require_once 'model/SshConnection.class.php';

  class ServerManager extends SshConnection {

    /**
     * Gets all servers from the DB by a userID
     * @return [boolean or arr] [Resturns a boolean if we don't have a server or a assoc array when we have servers]
     */
    public function getServers($userID) {
      $Db = new Db();
      $S = new Security();

      $sql = "SELECT idserver, serverName, serverIP FROM server WHERE userID=:userID";
      $input = array(
        "userID" => $S->checkInput($userID)
      );

      $Rows = $Db->countRows($sql, $input);
      if ($Rows >= 1) {
        // If we have a server
        $result = $Db->readData($sql, $input);
        return($result);
      }

      else {
        // We dont have a server
        return(false);
      }
    }



    /**
     * Adds a new server to the database
     * @param [arr] $newServer [A assoc array with the values from the vorm]
     * @param [string] $userMail  [The mail adress from the user]
     */
    public function addServer($newServer, $userMail) {
      $Db = new db();
      $S = new Security();
      $User = new User();

      $userID = $User->getUserID($S->checkInput($userMail));

      $sql = "INSERT INTO server (`serverName`, `serverIP`, `serverPort`, `serverUsername`, `serverPassword`, `userID`) VALUES (:serverName, :serverIP, :serverPort, :serverUsername, :serverPassword, :userID)";
      $input = array(
        "serverName" => $S->checkInput($newServer['serverName']),
        "serverIP" => $S->checkInput($newServer['serverIP']),
        "serverPort" => $S->checkInput($newServer['serverPort']),
        "serverUsername" => $S->checkInput($newServer['serverUsername']),
        "serverPassword" => $S->checkInput($newServer['serverPassword']),
        "userID" => $S->checkInput($userID)
      );

      $Db->CreateData($sql, $input);
    }

    /**
     * Method to update the server credentials
     * @param  [arr] $updateServer [The result from the form]
     * @param  [int] $serverID     [The serverID]
     */
    public function updateServer($updateServer, $serverID) {
      $Db = new db();
      $S = new Security();

      if (ISSET($updateServer['serverPassword']) && empty($updateServer['serverPassword'])) {
        // If we have a password update
        $sql = "UPDATE `server` SET serverName`=:serverName,`serverIP`=:serverIP,`serverPort`=:serverPort,`serverUsername`=:serverUsername,`serverPassword`=:serverPassword, WHERE idserver=:serverID";
        $input = array(
          "serverName" => $S->checkInput($updateServer['serverName']),
          "serverIP" => $S->checkInput($updateServer['serverIP']),
          "serverPort" => $S->checkInput($updateServer['serverPort']),
          "serverUsername" => $S->checkInput($updateServer['serverUsername']),
          "serverPassword" => $S->checkInput($updateServer['serverPassword']),
          "serverID" => $S->checkInput($serverID)
        );
      }

      else {
        // No new password
        $sql = "UPDATE `server` SET serverName`=:serverName,`serverIP`=:serverIP,`serverPort`=:serverPort,`serverUsername`=:serverUsername WHERE idserver=:serverID";
        $input = array(
          "serverName" => $S->checkInput($updateServer['serverName']),
          "serverIP" => $S->checkInput($updateServer['serverIP']),
          "serverPort" => $S->checkInput($updateServer['serverPort']),
          "serverUsername" => $S->checkInput($updateServer['serverUsername']),
          "serverID" => $S->checkInput($serverID)
        );
      }

      $result = $Db->updateData($sql, $input);
    }


    /**
     * Gets the name from the server by the serverID
     * @param  [int] $serverID [The ID from the server]
     * @return [string]           [The error message or the serverName from the DB]
     */
    public function getServerName($serverID) {
      $S = new Security();
      $Db = new db();

      $sql = "SELECT serverName FROM server WHERE idserver=:serverID LIMIT 1";
      $input = array(
        "serverID" => $S->checkInput($serverID)
      );
      $result = $Db->readData($sql, $input);
      if (!empty($result)) {
        // We have a serverName
        foreach ($result as $key) {
          return($key['serverName']);
          break;
        }
      }

      else {
        return('No server found');
      }
    }

    /**
     * Gets the userName from a server
     * @param  [int] $serverID [The ID of the server]
     * @return [string]           [With the result from the db or the error message]
     */
    public function getServerUsername($serverID) {
      $S = new Security();
      $Db = new db();

      $sql = "SELECT serverUsername FROM server WHERE idserver=:serverID LIMIT 1";
      $input = array(
        "serverID" => $S->checkInput($serverID)
      );
      $result = $Db->readData($sql, $input);

      if (!empty($result)) {
        foreach ($result as $key) {
          return($key['serverUsername']);
          break;
        }
      }

      else {
        // No server
        return('No server found');
      }

    }

    /**
     * Gets the server port from a server
     * @param  [INT] $serverID [The ID of the server]
     * @return [string]           [The result from the DB with the port or a error message that we din;t found the server]
     */
    public function getServerPort($serverID) {
      $S = new Security();
      $Db = new db();

      $sql = "SELECT serverPort FROM server WHERE idserver=:serverID LIMIT 1";
      $input = array(
        "serverID" => $S->checkInput($serverID)
      );

      $result = $Db->readData($sql, $input);
      if (!empty($result)) {
        // If we have a result
        foreach ($result as $key) {
          return($key['serverPort']);
          break;
        }
      }

      else {
        // NO server found
        return('No server found');
      }
    }

    /**
     * Gets the IP from a server by the serverID
     * @param  [int] $serverID [The ID of the server]
     * @return [string]           [With the serverIP or a error message that there is no serverIP]
     */
    public function getServerIP($serverID) {
      $S = new Security();
      $Db = new db();

      $sql = "SELECT serverIP FROM server WHERE idserver=:serverID LIMIT 1";
      $input = array(
        "serverID" => $S->checkInput($serverID)
      );
      $result = $Db->readData($sql, $input);

      if (!empty($result)) {
        // If we have a serverIP
        foreach ($result as $key) {
          return($key['serverIP']);
        }
      }

      else {
          // No result
          return('No serverIP found');
      }
    }

    /**
     * Gets the uptime from a server
     * @param  [INT] $serverID [The ID of the server]
     * @return [string]           [The result of the executed command or the error message no connection]
     */
    public function getServerUptime($serverID) {
      $S = new Security();
      $serverID = $S->checkInput($serverID);

      if ($this->sshLogin($serverID)) {
        // Check if there is a connection
        return($this->executeSshCommand('uptime -p'));
      }
      else {
        return('No connection to the server');
      }


    }

    /**
     * Gets the server RAM Usage
     * ANd converts it to something we can read
     * @param  [int] $serverID [The ID of the server]
     * @return [int]           [With the result of the command or the error message when connection has faild]
     */
    public function getServerRamUsage($serverID) {
      $S = new Security();
      $serverID = $S->checkInput($serverID);

      if ($this->sshLogin($serverID)) {
        $ramUsagePercentage = $this->executeSshCommand('free');

        $ramUsagePercentage = (string)trim($ramUsagePercentage);
        $ramUsagePercentage_arr = explode("\n", $ramUsagePercentage);
        // Gets each specefic things as a array
        $mem = explode(" ", $ramUsagePercentage_arr[1]);
        // Gets all things between a space
        $mem = array_filter($mem);
        // Gets all numbers
        $mem = array_merge($mem);
        $memory_usage = $mem[2]/$mem[1]*100;
        return(round($memory_usage));
      }
      else {
        return('No acces');
      }


    }

    /**
     * Gets the server CPU usage and returns it
     * @param  [int] $serverID [The ID of the server]
     * @return [string]           [With a error message or with the cpu usage]
     */
    public function getServerCPUUsage($serverID) {
      $S = new Security();
      $serverID = $S->checkInput($serverID);

        if ($this->sshLogin($serverID)) {
          // We have a shell
          $CPU = $this->executeSshCommand("echo $[100-$(vmstat 1 2|tail -1|awk '{print $15}')]");
          // To get the CPU usage from the server
          // https://askubuntu.com/questions/274349/getting-cpu-usage-realtime
          return($CPU);
        }

        else {
          return('No connection to the server');
        }



    }

    /**
     * Execute a command to a ssh server
     * @param  [string] $command [The command we want to execute]
     * @return [string]          [The result from the command]
     */
    private function executeSshCommand($command) {
      if ($this->sshConnectionActive) {
        // If we have a connection
        $sshShell = ssh2_exec($this->sshShell, $command);
        // Execute the command
        stream_set_blocking($sshShell, true);
        $sshResult = ssh2_fetch_stream($sshShell, SSH2_STREAM_STDIO);
        // Get the result from the server
        return(stream_get_contents($sshResult));
      }

      else {
        return("RUN ServerManager sshConnect! we have no connection");
      }

    }


    /**
     * Gets the online or offline server status
     * @param [string] $serverIP [The IP adress of the server]
     * @return [string] [With the status the server]
     */
    public function getServerStatus($serverIP) {
      $serverPing = exec("ping -c 1 $serverIP", $output, $status);
      // Ping result contains the text from the exec
      // status contains if the ping as been succeded
        if (0 == $status) {
            $status = "online";
        } else {
            $status = "offline";
        }

        return($status);
    }

    /**
     * To check if a user is owner of that server
     * @param  [int] $userID   [The ID of the user]
     * @param  [int] $serverID [The ID of the server]
     * @return [boolean]           [If a user has acces it returns true otherwise it returns false]
     */
    public function checkIfServerIsFromUser($userID, $serverID) {
      $Db = new db();
      $S = new Security();

      $sql = "SELECT idserver FROM server WHERE userID=:userID AND idserver=:serverID";
      $input = array(
        "userID" => $S->checkInput($userID),
        "serverID" => $S->checkInput($serverID)
      );

      $rows = $Db->countRows($sql, $input);

      if ($rows == 1) {
        // The user has acces to that server
        return(true);
      }
      else {
        // User hasn't acces to the server
        return(false);
      }
    }

  }


?>
