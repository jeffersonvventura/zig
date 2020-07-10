<?php
namespace App\Models;

use System\Model\Model;
use System\Auth\Auth; 
use App\Config\ConfigPerfil;

class Usuario extends Model
{
    use Auth; 
    
    protected $table = 'usuarios';
    protected $timestamps = true;

    public function __construct()
    {
    	parent::__construct();
    }

    public function usuarios($idEmpresa, $idUsuarioLogado = false, $idPerfilUsuarioLogado = false)
    {   
        # Se o perfil do Usuário logado não for (1), não traz Usuários com este perfil
        $queryCondicional = false;
        if ($idPerfilUsuarioLogado && $idPerfilUsuarioLogado == ConfigPerfil::superAdmin()) {
           $queryCondicional = " AND usuarios.id_perfil = 1";
        } else {
            $queryCondicional = " AND usuarios.id_perfil != 1";
        }
        
        # Se o perfil do Usuário logado for de vendedor, mostra apenas os dados do proprio Usuário
        if ($idPerfilUsuarioLogado && $idPerfilUsuarioLogado == ConfigPerfil::vendedor()) {
            $queryCondicional = " AND usuarios.id = {$idUsuarioLogado}";
        }

    	return $this->query(
    		"SELECT 
            usuarios.id AS id, usuarios.nome,
            usuarios.email, usuarios.id_sexo, 
            usuarios.created_at, usuarios.imagem,
            sexos.descricao, perfis.descricao AS perfil

            FROM usuarios INNER JOIN sexos ON 
    		usuarios.id_sexo = sexos.id 
            INNER JOIN perfis ON usuarios.id_perfil = perfis.id
            WHERE usuarios.id_empresa = {$idEmpresa} {$queryCondicional}"
    	);
    }
}