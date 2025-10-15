<?php
namespace App\Taxe;

class CalculatorTaxe
{
    public function calculerTVA(float $prixHt){
        $TVA = 0.2 * $prixHt;
        return $TVA;
    }

    public function calculerTTC(float $prixHt){
        $TTC = (0.2*$prixHt) + $prixHt;
        return $TTC;
    }

}