<?php

class xGeoLookup {
   
   private $Flagarray               = null;
   private $Flagarray_DXCC          = null;
   private $Flagfile                = null;
   
   public function SetFlagFile($Flagfile) {
      if (file_exists($Flagfile) && (is_readable($Flagfile))) {
         $this->Flagfile = $Flagfile;
         return true;
      }
      return false;
   }
    
   public function LoadFlags() {
      if ($this->Flagfile != null) {
         $this->Flagarray = array();
         $this->Flagarray_DXCC = array();
         $handle = fopen($this->Flagfile,"r");
         if ($handle) {
            $i = 0;
            while(!feof($handle)) {
               $row = fgets($handle,1024);
               $tmp = explode(";", $row);
         
               if (isset($tmp[0])) { $this->Flagarray[$i]['Country'] = $tmp[0]; } else { $this->Flagarray[$i]['Country'] = 'Undefined'; }
               if (isset($tmp[1])) { $this->Flagarray[$i]['ISO']     = $tmp[1]; } else { $this->Flagarray[$i]['ISO'] = "Undefined"; }
               if (isset($tmp[2])) { 
                  $tmp2 = explode("-", $tmp[2]);
                  for ($j=0;$j<count($tmp2);$j++) {
                     $this->Flagarray_DXCC[ trim($tmp2[$j]) ] = $i;
                  }
               }
               $i++; 
            }
            fclose($handle);
         }
         return true;
      }
      return false;
   }
   
   public function GetFlag($callsign) {
      $Image     = "";
      $Letters = 4;
      $Name = "";
      while ($Letters >= 2) {
         $Prefix = substr(trim($callsign), 0, $Letters);
               if (isset($this->Flagarray_DXCC[$Prefix])) {
                  $Image = $this->Flagarray[ $this->Flagarray_DXCC[$Prefix] ]['ISO'];
                  $Name  = $this->Flagarray[ $this->Flagarray_DXCC[$Prefix] ]['Country'];
                  break;
               }
         $Letters--;
      }
      return array(strtolower($Image), $Name);
   }
} 
?>
