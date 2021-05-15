<?php

namespace App\Classes;

use BCAParser\BCAParser;
use \App\Models\KlikbcaTransaction;

class KlikBCA
{
    protected $username = 'irwansis1022', $password = '140986';
    protected $date_from, $date_to;
    protected $bca_parser, $parsed_data;

    public function __construct()
    {
        $this->bca_parser = new BCAParser($this->username, $this->password);
    }

    /*
     * Get all transactions
     */
    public function mutasiSemua($date_from, $date_to)
    {
        $this->date_from = $date_from;
        $this->date_to = $date_to;

        $this->parsed_data = $this->bca_parser->getListTransaksi($date_from, $date_to);
        
        $this->bca_parser->logout();

        return $this;
    }

    /*
     * Get all DBs (money out) from given date params
     */
    public function mutasiUangKeluar($date_from, $date_to)
    {
        $this->date_from = $date_from;
        $this->date_to = $date_to;

        $this->parsed_data = $this->bca_parser->getTransaksiDebit($date_from, $date_to);
        
        $this->bca_parser->logout();

        return $this;
    }

    /*
     * Get all CRs (money in) from given date params
     */
    public function mutasiUangMasuk($date_from, $date_to)
    {
        $this->date_from = $date_from;
        $this->date_to = $date_to;

        $this->parsed_data = $this->bca_parser->getTransaksiCredit($date_from, $date_to);

        $this->bca_parser->logout();

        return $this;
    }

    /*
     * Get saldo akhir
     */
    public function saldo()
    {
        $this->parsed_data = $this->bca_parser->getSaldo();
    }

    public function get()
    {
        return $this->parsed_data;
    }

    public function save()
    {
        $filename = 'mutasi-bca_' . $this->date_from . '-' . $this->date_to . '.json';

        file_put_contents($filename, json_encode($this->parsed_data));
    }

    public function store()
    {   
        for ($i=0; $i<count($this->parsed_data); $i++)
        {
            KlikbcaTransaction::updateOrCreate([
                'date' => $this->parsed_data[$i]['date'],
                'description' => json_encode($this->parsed_data[$i]['description']),
                'flows' => $this->parsed_data[$i]['flows']
            ]);
        }
    }
}