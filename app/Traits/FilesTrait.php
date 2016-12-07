<?php
/**
 * Created by PhpStorm.
 * User: JudeParfait
 * Date: 13/07/2016
 * Time: 11:53
 */

namespace App\Traits;

use App\Traits\DateUtilitiesTrait;

trait FilesTrait
{
    use DateUtilitiesTrait;

    /**
     * Vérou et gère un fichier de liste d'attente
     * @param   string $path chemin complet du fichier
     * @param   string $uid UID de la ligne à traiter
     * @param   int $action action à effectuer (0: libérer la ligne, 1: bloquer et récupérer la ligne, -1: supprimer la ligne)
     * @param   string $lockcode code de bloquage, nécessaire pour libérer et supprimer
     * @param   bool|true $waitIfLocked détermine s'il faut attendre en cas de fichier déjà verrouillé
     * @return  bool|string                 retourne la ligne lue ou le résultat de l'opération
     */
    private function filequeueLockAndManage($path, $uid, $action, $lockcode = "0", $waitIfLocked = true)
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
                    $convert = explode("\n", trim($data));                //create array separate by new line

                    //d($data, "data");
                    $last_i = 0;
                    //
                    $readline = false;

                    $emptyRemoved = array_filter($convert);
                    $convert = array_filter($convert);
                    // search line
                    $countline = count($convert);
                    for ($i = 0; $i < $countline; $i++) {
                        if (empty($convert[$i])) {
                            // empty line
                        } else {
                            $line_tab = explode("|", $convert[$i]);
                            $curr_uid = trim($line_tab[0]);
                            $curr_lck = trim($line_tab[1]);

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
                        $last_i = $i;
                    }

                    if ($readline == false) {
                        //
                    } else {
                        $data = implode("\n", $convert);

                        //dd($countline, $last_i, $convert, $data, $emptyRemoved);

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

    /**
     * Vérou et écrit dans un fichier
     * @param string $path chemin complet du fichier
     * @param string $text ligne à écrire (rajouter) dans le fichier
     * @param bool|true $waitIfLocked détermine s'il faut attendre en cas de fichier déjà verrouillé
     * @return bool                         retourne le résultat de l'opération
     */
    private function fileLockAndWrite($path, $text, $waitIfLocked = true)
    {
        // Write OK
        $fileOk = false;
        $waitmax = 10;
        $wait = true;

        while ($wait) {
            //Open the File Stream
            $handle = fopen($path, 'a');

            //Lock File, error if unable to lock
            if (flock($handle, LOCK_EX)) {
                $fileOk = true;
                //$count = fread($handle, filesize($path));    //Get Current Hit Count

                //$lastcount = $count;

                //$count = $count + 1;    //Increment Hit Count by 1
                //ftruncate($handle, 0);    //Truncate the file to 0
                //rewind($handle);           //Set write pointer to beginning of file
                //fwrite($handle, $text);    //Write the new Hit Count
                $contenu = $text . "\r\n";
                fputs($handle, $contenu);
                flock($handle, LOCK_UN);    //Unlock File
            } else {
                $fileOk = false;
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

        return $fileOk;
    }

    private function readLines($fileName)
    {
        // Si le fichier est inexistant, on ne continue pas
        if (!$file = fopen($fileName, 'r')) {
            return;
        }

        // Tant qu'il reste des lignes à parcourir
        while (($line = fgets($file)) !== false) {
            // On dit à PHP que cette ligne du fichier fait office de "prochaine entrée du tableau"
            yield $line;
        }

        fclose($file);
    }

    private function newTracestep($title)
    {
        return [
            'id' => null,
            'title' => $title,
            'execode' => 0,
            'exestring' => null,
            'result' => null,
            'start_at' => $this->getNowDateTime(),
            'end_at' => null,
            'time' => ""
        ];
    }

    private function addTraceStep(&$trace, $tracestep, $execode, $exestring, $result)
    {
        // Obtention de la pile complète sous forme de tableau
        if (empty($trace)) {
            $trace_tmp = array();
        } else {
            $trace_tmp = json_decode($trace, true);
        }

        // Affectation de l'id de l'étape courante
        $tracestep['id'] = count($trace_tmp) + 1;

        list($tracestep['execode'], $tracestep['exestring'], $tracestep['result'], $tracestep['end_at']) = [
            $execode, $exestring, $result, $this->getNowDateTime()
        ];

        $tracestep['time'] = $this->diffDateTime($tracestep['start_at'], $tracestep['end_at']);

        $trace_tmp[] = $tracestep;
        $trace = json_encode($trace_tmp);

        return $trace;
    }

    private function deleteFile($path)
    {
        return \File::Delete($path);
    }

    private function moveFile($from, $to)
    {
        return \File::move($from, $to);
    }

    private function copyFile($from, $to)
    {
        return \File::copy($from, $to);
    }

    private function fileArray($name, $type, $real_path, $error, $size)
    {
        return ['name' => $name, 'type' => $type, 'tmp_name' => $real_path, 'error' => $error, 'size' => $size];
    }
}
