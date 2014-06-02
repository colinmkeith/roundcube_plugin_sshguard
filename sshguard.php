<?php
/**
 * RoundCube SSHGuard Plugin
 *
 * @version 0.1
 * @author Colin Keith [colin@hagenhosting.com]
 * @url 
 * @license GPLv3
 */
class sshguard extends rcube_plugin
{
  function init()
  {
    $this->add_hook('login_failed', array($this, 'log'));
  }

  function log($args)
  {
    $logprefix = sprintf('%s webmail ssh[999] ', strftime('%h %e %T '));
    $logMsg1 = sprintf('pam_unix(sshd:auth): authentication failure; logname= uid=0 euid=0 tty=ssh ruser= rhost=%s user=%s', getenv('REMOTE_ADDR'), $args['user']);
    $logMsg2 = sprintf('Failed password for %s from %s port 999 ssh2', $args['user'], getenv('REMOTE_ADDR'));

    $logMsg = $logprefix . $logMsg1 . "\n" . $logprefix . $logMsg2 . "\n";

    $logFH = fopen('/var/log/webmail_block.log', 'a');
    fprintf($logFH, $logMsg);
    fprintf($logFH, $logMsg);
    fprintf($logFH, $logMsg);
    fclose($logFH);
  }

}

?>
