<?php
/**
 * Created by PhpStorm.
 * User: JudeParfait
 * Date: 26/07/2016
 * Time: 16:34
 */

namespace App\Commands\SMS;

use App\Commands\SMS\SMPP;
use App\Commands\SMS\SMPP2;

class Sms
{
    private $smpp_host;
    private $smpp_port;
    private $smsc_systemid;
    private $smsc_password;
    private $smsc_systemtype;


    public function __construct()
    {
        //$settings = parse_ini_file("config/settings.ini.php");

        $this->smpp_host = env('SMPP_HOST');
        $this->smpp_port = env('SMPP_PORT');
        $this->smsc_systemid = env('SMSC_SYSTEMID');
        $this->smsc_password = env('SMSC_PASSWORD');
        $this->smsc_systemtype = env('SMSC_SYSTEMTYPE');
    }

    public function send($message, $from, $to, $replace = null, $smstype = 1)
    {
        $msg = urlencode($message);
        $send_rslt = file_get_contents("http://10.32.15.237/GTSMSC/send_smppclient.php?From=" . $from . "&To=" . $to . "&Msg=" . $msg);
        return 1;
        /*if (is_null($replace)) {
            $sms = $message;
        } else {
            $sms = strtr($message, $replace);
        }

        $is_recursiv = $this->checkRecursiveness($from, $sms, $to);

        if ($is_recursiv) {
            // Recursivité détectée
            return -2;
        } else {
            if ($smstype == 1) {
                $this->send_message($sms, $from, $to);
                return 1;
            } elseif ($smstype == 2) {
                $this->send_message_flash($sms, $from, $to);
                return 1;
            } else {
                return -1;
            }
        }*/
    }

    private function checkRecursiveness($smsfrom, $sms, $smsto)
    {
        $recursiv_result = false;

        return $recursiv_result;
    }

    private function getsmshystory($path, $historydelay, $smsfrom, $sms, $smsto, $uid, $action, $lockcode = "0", $waitIfLocked = true)
    {
        // Write OK
        $fileOk = false;
        $waitmax = 10;
        $wait = true;

        $readline = "";

        while ($wait) {

            //Open the File Stream
            $handle = fopen($path, "r+");

            //Lock File, error if unable to lock
            if (flock($handle, LOCK_EX)) {
                $fileOk = true;

                if (filesize($path) > 0) {

                    $data = fread($handle, 8192);        //read the file
                    $convert = explode("\n", $data);                //create array separate by new line

                    //d($data, "data");
                    //
                    $readline = false;
                    $countline = count($convert);
                    $found = false;
                    // search line
                    for ($i = 0; $i < $countline; $i++) {
                        if (empty($convert[$i])) {
                            // empty line
                        } else {

                            $line_tab = explode("|", $convert[$i]);
                            $curr_smsfrom = trim($line_tab[0]);
                            $curr_smsto = trim($line_tab[1]);
                            $curr_sms = trim($line_tab[2]);
                            $curr_smstimestamp = trim($line_tab[3]);
                            $curr_smsdate = trim($line_tab[4]);

                            if (($curr_smsfrom == $smsfrom) && ($curr_smsto == $smsto) && ($curr_sms == $sms)) {
                                //
                            }

                            // Actions
                            if (($action == "0")) {
                                if ($curr_uid == $uid) {
                                    if ($curr_lck == $lockcode) {
                                        // Free line
                                        $line_tab[1] = $action;
                                        $convert[$i] = implode("|", $line_tab);
                                        $readline = true;
                                    } else {
                                        $readline = false;
                                        $i = $countline;
                                    }
                                } else {
                                    $readline = false;
                                }
                            } elseif ($action == "1") {
                                // Take and Lock line
                                if ($curr_lck == "0") {
                                    $new_lck = uniqid("lck_");

                                    $line_tab[1] = $new_lck;
                                    $readline = explode("|", $curr_uid . "|" . $new_lck);
                                    $convert[$i] = implode("|", $line_tab);
                                    $i = $countline;
                                } else {
                                    // line already locked
                                    $readline = false;
                                }
                            } elseif ($action == "-1") {

                                if (($curr_uid == $uid)) {

                                    if ($curr_lck == $lockcode) {
                                        // Delete line
                                        unset($convert[$i]);
                                        $readline = true;
                                        $i = $countline;
                                    } else {
                                        $readline = false;
                                        $i = $countline;
                                    }
                                } else {
                                    $readline = false;
                                }
                            } else {
                                // Bad action
                                $readline = false;
                                $i = $countline;
                            }
                        }
                    }

                    if ($readline == false) {
                        //
                    } else {
                        $data = implode("\n", $convert);

                        ftruncate($handle, 0);                //Truncate the file to 0
                        rewind($handle);                    //Set write pointer to beginning of file
                        fwrite($handle, $data);                //Write the new Hit Count
                    }

                    flock($handle, LOCK_UN);                //Unlock File
                } else {
                    $readline = false;
                }
            } else {
                $fileOk = false;
                $readline = false;
                //echo "Could not Lock File!";
            }
            //Close Stream
            fclose($handle);

            if (!($fileOk) && ($waitIfLocked)) {

                if ($waitmax > 0) {
                    $wait = true;
                    $waitmax = $waitmax - 1;

                    sleep(1);
                } else {
                    $wait = false;
                }

            } else {
                $wait = false;
            }
        }

        return $readline;
    }

    private function send_message($message, $from, $to)
    {
        try {
            $tx = new SMPP($this->smpp_host, $this->smpp_port);
            //$tx->debug=true;
            $tx->system_type = "SMPP";
            $tx->addr_npi = 1;
            //print "open status: ".$tx->state."\n";
            $tx->bindTransmitter($this->smsc_systemid, $this->smsc_password);
            // Je modifie la valeur par defaut 1 de la variable sms_source_addr_npi par 0
            $tx->sms_source_addr_npi = 0;
            //$tx->sms_source_addr_ton=1;
            $tx->sms_dest_addr_ton = 1;
            $tx->sms_dest_addr_npi = 1;
            $tx->sendSMS($from, $to, $message);
            $tx->close();
            unset($tx);

        } catch (Exception $e) {
            $err_msg = "AN ERROR OCCURED: " . $e->getMessage() . " <br>At code: " . $e->getCode() . " <br>At file: " . $e->getFile() . " <br>At line: " . $e->getLine() . " <br>At traceString: " . $e->getTraceAsString();
        }
    }

    private function send_message_flash($message, $from, $to)
    {
        try {
            $smpp = new SMPP2();
            $smpp->SetSender($from);
            $smpp->Start($this->smpp_host, $this->smpp_port, $this->smsc_systemid, $this->smsc_password, $this->smsc_systemtype);

            // Envoi de message flash
            $smpp->Send("$to", "$message");

        } catch (Exception $e) {
            $err_msg = "AN ERROR OCCURED: " . $e->getMessage() . " <br>At code: " . $e->getCode() . " <br>At file: " . $e->getFile() . " <br>At line: " . $e->getLine() . " <br>At traceString: " . $e->getTraceAsString();
        }
    }
}
