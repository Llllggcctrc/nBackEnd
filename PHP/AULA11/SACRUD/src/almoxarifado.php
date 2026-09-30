<?php
declare(strict_types=1);

//Camada de Acesso a Dados (DAO) para almoxarifado
//essa Camada é uma class - usa Paradigma de Progamação Orientada ao Objeto

final class almoxarifadoDAO{
    //atributos ->
    private PDO $pdo; 

    // Métodos -> ações
    //métodos que toda classe tem -> Construtor -> permite instanciar objetos
    public function __construct(PDO $pdo){
        $this->pdo = $pdo;
    }

    // métodos do CRUD


}