<?php

  class SshConnection {

    protected $serverIP;
    protected $serverPort;
    protected $serverUsername;
    protected $serverPassword;

    protected $sshShell;
    // The active ssh connection lives here

    protected $sshConnectionActive;
    // If the connection is active


    public function checkIfWeCanUseSSHLogin($serverID) {
      $this->getServerCredentials($serverID);
      $this->sshConnect();
      return($this->sshConnected);
    }

    /**
     * Starts a ssh connection
     * @return [string on fail or boolean on succes] [The result form the connection and auth]
     */
    protected function sshConnect() {
      $this->sshShell = ssh2_connect($this->serverIP, $this->serverPort);
      // Start the connection
      if ($this->sshShell != false) {
        // We can connect to the server
        if (ssh2_auth_password($this->sshShell, $this->serverUsername, $this->serverPassword)) {
          // connection is a succes
          $this->sshConnectionActive = true;
          return(true);
        }
        // Start the auth

        else {
          // The auth has failt
          $this->sshConnectionActive = false;
          die('Wrong server username or password');
        }
      }

      else {
        // The connection is failt
        return('No connection with the server');
      }
    }


    /**
     * Gets the server credentials and puts them in the class properties
     * @param [int] $serverID [The ID of the server]
     */
    protected function getServerCredentials($serverID) {
      $Db = new db();
      $S = new Security();

      $sql = "SELECT * FROM server WHERE idserver=:serverID";
      $input = array(
        "serverID" => $S->checkInput($serverID)
      );

      $result = $Db->readData($sql, $input);
      if (!empty($result)) {
        foreach ($result as $key) {
          $this->serverIP = $key['serverIP'];
          $this->serverPort = $key['serverPort'];
          $this->serverUsername = $key['serverUsername'];
          $this->serverPassword = $key['serverPassword'];
          break;
        }
      }

      else {
        // When there isn't a server
        return(false);
      }

    }
  }


?>