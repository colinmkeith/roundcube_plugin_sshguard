<?php
/**
 * RoundCube SSHGuard Plugin
 *
 * @version 0.2
 * @author Colin Keith [colin@hagenhosting.com]
 * @url https://github.com/colinmkeith/roundcube_plugin_sshguard
 * @license Apache License v2.0
 */
class sshguard extends rcube_plugin
{
  function init()
  {
    $this->add_hook('login_failed', array($this, 'log_to_sshguard'));
    $default = array(
      'logfile' => '/var/log/webmail_block.log',
      'fake_username' => 'webmail',
      'fake_pid'      => 999,
      'fake_port'     => 999,
    );

    $rcmail_config['sshguard_plugin'] = array_merge($def, $rcmail_config['sshguard_plugin']);
  }

  function log_to_sshguard($args)
  {
    $rcmail = rcmail::get_instance();
    $this->load_config();
    $config = $rcmail->config->get('sshguard_plugin');

    $logprefix = sprintf('%s %s ssh[%d] ',
      strftime('%h %e %T'),
      $sshguard_config['fake_username'],
      $sshguard_config['fake_pid']
    );
    $logMsg1 = sprintf('pam_unix(sshd:auth): authentication failure; logname= uid=0 euid=0 tty=ssh ruser= rhost=%s user=%s',
      getenv('REMOTE_ADDR'),
      $args['user']
    );

    $logMsg2 = sprintf('Failed password for %s from %s port %s ssh2',
      $args['user'],
      getenv('REMOTE_ADDR'),
      $sshguard_config['fake_port']
    );

    $logMsg = $logprefix . $logMsg1 . "\n" . $logprefix . $logMsg2 . "\n";

    $logFH = fopen($sshguard_config['logfile'], 'a');
    $write_count = $sshguard_config['write_count'] || 3;
    for($i=0; $i<$write_count; $i++) {
      fprintf($logFH, $logMsg);
    }
    fclose($logFH);
  }
}

?>
